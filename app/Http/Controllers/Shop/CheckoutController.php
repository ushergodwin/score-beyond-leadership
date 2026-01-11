<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Services\Payments\PesapalPaymentService;
use App\Support\Cart\CartManager;
use App\Support\Currency\PriceDisplay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartManager $cart,
        private readonly PesapalPaymentService $pesapal,
    ) {
    }

    public function index(): Response|RedirectResponse
    {
        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('shop.index')
                ->with('flash.cart_message', 'Your cart is empty. Add items before checking out.');
        }

        $shippingMethods = ShippingMethod::query()
            ->where('is_active', true)
            ->orderBy('is_pickup')
            ->orderBy('name')
            ->get()
            ->map(function (ShippingMethod $method) {
                $rate = PriceDisplay::forUgx($method->base_rate);

                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'code' => $method->code,
                    'type' => $method->type,
                    'region' => $method->region,
                    'carrier' => $method->carrier,
                    'is_pickup' => $method->is_pickup,
                    'eta' => $this->formatEta($method->estimated_min_days, $method->estimated_max_days),
                    'rate' => $rate,
                ];
            });

        $cartSummary = [
            'items' => $cartItems->map(fn (array $item) => [
                'id' => $item['id'],
                'name' => $item['name'],
                'variant_name' => $item['variant_name'],
                'quantity' => $item['quantity'],
                'display_price' => $item['display_price'],
                'unit_price' => $item['unit_price'],
                'currency' => $item['currency'],
                'image' => $item['image'],
                'slug' => $item['slug'],
                'sku' => $item['sku'],
                'stock' => $item['stock'] ?? 0,
            ])->values(),
            'totals' => $this->cart->totals(),
        ];

        $prefill = [
            'first_name' => null,
            'last_name' => null,
            'email' => null,
            'phone' => null,
            'country' => 'UG',
            'city' => null,
            'state' => null,
            'postal_code' => null,
            'line_one' => null,
            'line_two' => null,
        ];

        // If user is logged in, get their customer info and last shipping address
        if (Auth::check()) {
            $user = Auth::user();
            $customer = Customer::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if ($customer) {
                $prefill['first_name'] = $customer->first_name;
                $prefill['last_name'] = $customer->last_name;
                $prefill['email'] = $customer->email;
                $prefill['phone'] = $customer->phone;

                // Get last used shipping address (from most recent order or default shipping address)
                $lastAddress = $customer->addresses()
                    ->where('is_default_shipping', true)
                    ->latest()
                    ->first();

                // If no default, get from most recent order
                if (!$lastAddress) {
                    $lastOrder = $customer->orders()
                        ->with('shippingAddress')
                        ->latest()
                        ->first();

                    if ($lastOrder && $lastOrder->shippingAddress) {
                        $lastAddress = $lastOrder->shippingAddress;
                    }
                }

                if ($lastAddress) {
                    $prefill['country'] = $lastAddress->country ?? 'UG';
                    $prefill['city'] = $lastAddress->city;
                    $prefill['state'] = $lastAddress->state;
                    $prefill['postal_code'] = $lastAddress->postal_code;
                    $prefill['line_one'] = $lastAddress->line_one;
                    $prefill['line_two'] = $lastAddress->line_two;
                }
            } else {
                // Fallback to user info if no customer record
                $prefill['first_name'] = $user->name;
                $prefill['email'] = $user->email;
            }
        }

        return Inertia::render('Shop/Checkout', [
            'cart' => $cartSummary,
            'shippingMethods' => $shippingMethods,
            'prefill' => $prefill,
        ]);
    }

    public function store(CheckoutRequest $request): Response|RedirectResponse|HttpResponse
    {
        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('shop.index')
                ->with('flash.cart_message', 'Your cart became empty. Add items before checking out.');
        }

        $validated = $request->validated();

        /** @var ShippingMethod $shippingMethod */
        $shippingMethod = ShippingMethod::query()
            ->where('is_active', true)
            ->findOrFail($validated['shipping_method_id']);

        // Mobile Money is now available for all shipping methods including international
        // No restriction needed

        $order = DB::transaction(function () use ($validated, $shippingMethod, $cartItems, $request) {
            $customer = Customer::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'user_id' => Auth::id(),
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'],
                    'preferred_currency' => 'UGX',
                    'is_subscribed' => (bool) ($validated['subscribe'] ?? false),
                ]
            );

            $shippingAddress = Address::create([
                'addressable_id' => $customer->id,
                'addressable_type' => Customer::class,
                'label' => 'Shipping',
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'line_one' => $validated['line_one'],
                'line_two' => $validated['line_two'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'],
                'is_default_shipping' => true,
                'is_default_billing' => true,
            ]);

            $subtotal = $cartItems->reduce(
                fn (float $carry, array $item) => $carry + ($item['unit_price'] * $item['quantity']),
                0.0
            );

            $shippingTotal = (float) $shippingMethod->base_rate;
            $grandTotal = $subtotal + $shippingTotal;

            /** @var Order $order */
            $order = Order::create([
                'customer_id' => $customer->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $shippingAddress->id,
                'shipping_method_id' => $shippingMethod->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'currency' => 'UGX',
                'exchange_rate' => 1,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_total' => $shippingTotal,
                'tax_total' => 0,
                'grand_total' => $grandTotal,
                'ip_address' => $request->ip(),
                'customer_note' => $validated['customer_note'] ?? null,
                'metadata' => [
                    'user_agent' => $request->userAgent(),
                    'payment_method' => $validated['payment_method'],
                    'create_account' => (bool) ($validated['create_account'] ?? false),
                    'account_password' => $validated['create_account'] ? ($validated['password'] ?? null) : null,
                ],
                'placed_at' => now(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'sku' => $item['sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => 0,
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                    'currency' => 'UGX',
                    'meta' => [
                        'variant_name' => $item['variant_name'],
                    ],
                ]);
            }

            return $order;
        });

        try {
            // Build payload according to Pesapal API 3.0 documentation
            // https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/submitorderrequest
            $payload = [
                'id' => $order->order_number, // Merchant reference (max 50 chars, must be unique)
                'currency' => $order->currency, // ISO Currency Code
                'amount' => (float) $order->grand_total,
                'description' => "Score Beyond Order {$order->order_number}", // Max 100 chars
                'callback_url' => route('payments.pesapal.callback'),
                'notification_id' => (string) config('services.pesapal.ipn_id'), // IPN notification ID (GUID)
                'billing_address' => [
                    'phone_number' => $validated['phone'],
                    'email_address' => $validated['email'],
                    'country_code' => $validated['country'] ?? 'UG',
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'line_1' => $validated['line_one'],
                    'line_2' => $validated['line_two'] ?? '',
                    'city' => $validated['city'] ?? '',
                    'state' => $validated['state'] ?? '',
                    'postal_code' => $validated['postal_code'] ?? '',
                ],
            ];

            $transaction = $this->pesapal->createPaymentIntent($order, $payload);

            $order->update([
                'pesapal_order_tracking_id' => $transaction->pesapal_tracking_id,
            ]);

            $this->cart->clear();

            // redirect_url is the payment URL to redirect customer to
            if (is_string($transaction->provider_reference) && $transaction->provider_reference !== '') {
                return Inertia::location($transaction->provider_reference);
            }

            return redirect()
                ->route('checkout.index')
                ->with('flash.cart_message', 'Order created, but we could not redirect you to Pesapal. Please try again.');
        } catch (Throwable $throwable) {
            report($throwable);

            return redirect()
                ->route('checkout.index')
                ->withErrors(['payment' => 'We were unable to initialize payment. Please try again.']);
        }
    }

    private function formatEta(?int $min, ?int $max): ?string
    {
        if ($min === null && $max === null) {
            return null;
        }

        if ($min !== null && $max !== null) {
            return "{$min}-{$max} business days";
        }

        $value = $min ?? $max;

        return "{$value}+ business days";
    }
}
