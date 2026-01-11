<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Order;
use App\Models\VolunteerApplication;
use App\Support\Currency\PriceDisplay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Get orders (by customer_id or customer email - handles guest checkout scenario)
        $ordersQuery = $user->getOrders();

        // Get donations (by customer_id or email)
        $donationsQuery = $user->getDonations();

        // Calculate statistics
        $totalOrders = (clone $ordersQuery)->count();
        $totalDonations = (clone $donationsQuery)->count();
        
        $totalSpent = (clone $ordersQuery)
            ->where('payment_status', 'completed')
            ->sum('grand_total');
        
        $totalDonated = (clone $donationsQuery)
            ->where('payment_status', 'completed')
            ->sum('amount');

        // Recent orders (last 5)
        $recentOrders = (clone $ordersQuery)
            ->with(['items'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'grand_total' => PriceDisplay::forUgx($order->grand_total),
                    'items_count' => $order->items->count(),
                    'placed_at' => $order->placed_at?->format('M d, Y'),
                    'created_at' => $order->created_at->format('M d, Y'),
                ];
            });

        // Recent donations (last 5)
        $recentDonations = (clone $donationsQuery)
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($donation) {
                return [
                    'id' => $donation->id,
                    'donation_number' => $donation->donation_number,
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'payment_status' => $donation->payment_status,
                    'is_recurring' => $donation->is_recurring,
                    'created_at' => $donation->created_at->format('M d, Y'),
                    'paid_at' => $donation->paid_at?->format('M d, Y'),
                ];
            });

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'total_orders' => $totalOrders,
                'total_donations' => $totalDonations,
                'total_spent' => PriceDisplay::forUgx($totalSpent),
                'total_donated' => [
                    'ugx' => $totalDonated,
                    'formatted' => number_format($totalDonated, 0) . ' (across all currencies)',
                ],
            ],
            'recent_orders' => $recentOrders,
            'recent_donations' => $recentDonations,
        ]);
    }

    public function orders(Request $request): Response
    {
        $user = $request->user();

        // Get orders (by customer_id or customer email - handles guest checkout scenario)
        $query = $user->getOrders()
            ->with(['items']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest('created_at')->paginate(15)->withQueryString();

        return Inertia::render('Dashboard/Orders/Index', [
            'orders' => [
                'data' => $orders->getCollection()->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'grand_total' => PriceDisplay::forUgx($order->grand_total),
                        'items_count' => $order->items->count(),
                        'placed_at' => $order->placed_at?->format('M d, Y H:i'),
                        'created_at' => $order->created_at->format('M d, Y H:i'),
                    ];
                }),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'links' => $orders->linkCollection()->toArray(),
                ],
            ],
            'filters' => $request->only(['status', 'payment_status', 'search', 'date_from', 'date_to']),
        ]);
    }

    public function showOrder(Request $request, string $orderNumber): Response
    {
        $user = $request->user();

        // Get order (by customer_id or customer email - handles guest checkout scenario)
        $order = $user->getOrders()
            ->where('order_number', $orderNumber)
            ->with(['items', 'shippingAddress', 'billingAddress', 'shippingMethod', 'paymentTransactions'])
            ->firstOrFail();

        // Build tracking timeline
        $timeline = [];
        $timeline[] = [
            'status' => 'order_placed',
            'label' => 'Order Placed',
            'date' => $order->placed_at ?? $order->created_at,
            'completed' => true,
        ];

        if ($order->payment_status === 'completed') {
            $timeline[] = [
                'status' => 'payment_confirmed',
                'label' => 'Payment Confirmed',
                'date' => $order->paymentTransactions()->where('status', 'completed')->first()?->paid_at ?? $order->placed_at,
                'completed' => true,
            ];
        }

        if (in_array($order->status, ['processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'processing',
                'label' => 'Processing',
                'date' => $order->updated_at,
                'completed' => true,
            ];
        }

        if (in_array($order->status, ['shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'shipped',
                'label' => 'Shipped',
                'date' => $order->updated_at,
                'completed' => true,
            ];
        }

        if ($order->status === 'delivered') {
            $timeline[] = [
                'status' => 'delivered',
                'label' => 'Delivered',
                'date' => $order->updated_at,
                'completed' => true,
            ];
        }

        return Inertia::render('Dashboard/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'currency' => $order->currency,
                'subtotal' => PriceDisplay::forUgx($order->subtotal),
                'shipping_total' => PriceDisplay::forUgx($order->shipping_total),
                'tax_total' => PriceDisplay::forUgx($order->tax_total),
                'grand_total' => PriceDisplay::forUgx($order->grand_total),
                'placed_at' => $order->placed_at?->format('M d, Y H:i'),
                'created_at' => $order->created_at->format('M d, Y H:i'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'sku' => $item->sku,
                        'quantity' => $item->quantity,
                        'unit_price' => PriceDisplay::forUgx($item->unit_price),
                        'subtotal' => PriceDisplay::forUgx($item->subtotal),
                    ];
                }),
                'shipping_address' => $order->shippingAddress ? [
                    'name' => $order->shippingAddress->name,
                    'line_1' => $order->shippingAddress->line_1,
                    'line_2' => $order->shippingAddress->line_2,
                    'city' => $order->shippingAddress->city,
                    'state' => $order->shippingAddress->state,
                    'postal_code' => $order->shippingAddress->postal_code,
                    'country' => $order->shippingAddress->country,
                    'phone' => $order->shippingAddress->phone,
                ] : null,
                'billing_address' => $order->billingAddress ? [
                    'name' => $order->billingAddress->name,
                    'line_1' => $order->billingAddress->line_1,
                    'line_2' => $order->billingAddress->line_2,
                    'city' => $order->billingAddress->city,
                    'state' => $order->billingAddress->state,
                    'postal_code' => $order->billingAddress->postal_code,
                    'country' => $order->billingAddress->country,
                    'phone' => $order->billingAddress->phone,
                ] : null,
                'shipping_method' => $order->shippingMethod ? [
                    'name' => $order->shippingMethod->name,
                    'description' => $order->shippingMethod->description,
                ] : null,
                'timeline' => $timeline,
            ],
        ]);
    }

    public function donations(Request $request): Response
    {
        $user = $request->user();
        $donationsQuery = $user->getDonations();

        // Apply filters
        if ($request->filled('payment_status')) {
            $donationsQuery->where('payment_status', $request->payment_status);
        }

        if ($request->filled('is_recurring')) {
            $donationsQuery->where('is_recurring', $request->boolean('is_recurring'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $donationsQuery->where(function ($q) use ($search) {
                $q->where('donation_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $donationsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $donationsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $donations = $donationsQuery->latest('created_at')->paginate(15)->withQueryString();

        return Inertia::render('Dashboard/Donations/Index', [
            'donations' => [
                'data' => $donations->getCollection()->map(function ($donation) {
                    return [
                        'id' => $donation->id,
                        'donation_number' => $donation->donation_number,
                        'amount' => $donation->amount,
                        'currency' => $donation->currency,
                        'payment_status' => $donation->payment_status,
                        'is_recurring' => $donation->is_recurring,
                        'frequency' => $donation->frequency,
                        'created_at' => $donation->created_at->format('M d, Y H:i'),
                        'paid_at' => $donation->paid_at?->format('M d, Y H:i'),
                    ];
                }),
                'meta' => [
                    'current_page' => $donations->currentPage(),
                    'last_page' => $donations->lastPage(),
                    'per_page' => $donations->perPage(),
                    'total' => $donations->total(),
                    'links' => $donations->linkCollection()->toArray(),
                ],
            ],
            'filters' => $request->only(['payment_status', 'is_recurring', 'search', 'date_from', 'date_to']),
        ]);
    }

    public function showDonation(Request $request, string $donationNumber): Response
    {
        $user = $request->user();
        $donation = $user->getDonations()
            ->where('donation_number', $donationNumber)
            ->with(['paymentTransactions'])
            ->firstOrFail();

        return Inertia::render('Dashboard/Donations/Show', [
            'donation' => [
                'id' => $donation->id,
                'donation_number' => $donation->donation_number,
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'payment_status' => $donation->payment_status,
                'is_recurring' => $donation->is_recurring,
                'frequency' => $donation->frequency,
                'impact_tier' => $donation->impact_tier,
                'message' => $donation->message,
                'first_name' => $donation->first_name,
                'last_name' => $donation->last_name,
                'email' => $donation->email,
                'phone' => $donation->phone,
                'country' => $donation->country,
                'organization' => $donation->organization,
                'address' => $donation->address,
                'tax_receipt_requested' => $donation->tax_receipt_requested,
                'created_at' => $donation->created_at->format('M d, Y H:i'),
                'paid_at' => $donation->paid_at?->format('M d, Y H:i'),
            ],
        ]);
    }

    public function downloadReceipt(Request $request, string $donationNumber)
    {
        $user = $request->user();
        $donation = $user->getDonations()
            ->where('donation_number', $donationNumber)
            ->where('payment_status', 'completed')
            ->firstOrFail();

        $pdf = Pdf::loadView('receipts.donation', [
            'donation' => $donation,
        ]);

        $filename = 'donation-receipt-' . $donation->donation_number . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function volunteerApplications(Request $request): Response
    {
        $user = $request->user();
        
        // Get volunteer applications by email
        $applicationsQuery = VolunteerApplication::where('email', $user->email)
            ->latest('created_at');

        // Apply filters
        $statusFilter = $request->query('status');
        if ($statusFilter) {
            $applicationsQuery->where('status', $statusFilter);
        }

        $programTypeFilter = $request->query('program_type');
        if ($programTypeFilter) {
            $applicationsQuery->where('program_type', $programTypeFilter);
        }

        $search = $request->query('search');
        if ($search) {
            $applicationsQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('preferred_volunteer_role', 'like', "%{$search}%");
            });
        }

        $applications = $applicationsQuery->paginate(15)->withQueryString();

        return Inertia::render('Dashboard/VolunteerApplications/Index', [
            'applications' => [
                'data' => $applications->getCollection()->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'status' => $application->status,
                        'program_type' => $application->program_type,
                        'first_name' => $application->first_name,
                        'last_name' => $application->last_name,
                        'preferred_volunteer_role' => $application->preferred_volunteer_role,
                        'availability_start' => $application->availability_start?->format('Y-m-d'),
                        'availability_end' => $application->availability_end?->format('Y-m-d'),
                        'payment_status' => $application->payment_status,
                        'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'meta' => [
                    'current_page' => $applications->currentPage(),
                    'last_page' => $applications->lastPage(),
                    'per_page' => $applications->perPage(),
                    'total' => $applications->total(),
                    'links' => $applications->linkCollection()->toArray(),
                ],
            ],
            'filters' => [
                'status' => $statusFilter,
                'program_type' => $programTypeFilter,
                'search' => $search,
            ],
        ]);
    }

    public function showVolunteerApplication(Request $request, int $id): Response
    {
        $user = $request->user();
        
        $application = VolunteerApplication::where('email', $user->email)
            ->findOrFail($id);

        return Inertia::render('Dashboard/VolunteerApplications/Show', [
            'application' => [
                'id' => $application->id,
                'status' => $application->status,
                'program_type' => $application->program_type,
                'preferred_name' => $application->preferred_name,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'date_of_birth' => $application->date_of_birth?->format('Y-m-d'),
                'nationality' => $application->nationality,
                'email' => $application->email,
                'phone' => $application->phone,
                'country_of_residence' => $application->country_of_residence,
                'city' => $application->city,
                'preferred_volunteer_role' => $application->preferred_volunteer_role,
                'preferred_roles' => $application->preferred_roles,
                'availability_start' => $application->availability_start?->format('Y-m-d'),
                'availability_end' => $application->availability_end?->format('Y-m-d'),
                'length_of_stay_weeks' => $application->length_of_stay_weeks,
                'tshirt_size' => $application->tshirt_size,
                'skills_experience' => $application->skills_experience,
                'payment_status' => $application->payment_status,
                'created_at' => $application->created_at->format('M d, Y H:i'),
                'updated_at' => $application->updated_at->format('M d, Y H:i'),
            ],
        ]);
    }
}
