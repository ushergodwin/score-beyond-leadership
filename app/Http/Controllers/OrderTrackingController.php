<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderTrackingResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class OrderTrackingController extends Controller
{
    /**
     * Track an order by order number and optional token.
     * 
     * Dual access logic:
     * - Logged-in users: Must own the order (via customer relationship or email match)
     * - Guests: Must provide valid tracking_token that matches the order
     */
    public function show(Request $request, string $orderNumber): JsonResponse
    {
        // Rate limiting: 10 requests per minute per IP
        $key = 'order-tracking:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
            ], 429);
        }
        RateLimiter::hit($key, 60); // 60 seconds = 1 minute

        // Find order by order number
        $order = Order::where('order_number', $orderNumber)
            ->with(['items', 'shippingMethod', 'paymentTransactions'])
            ->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order not found.',
            ], 404);
        }

        $user = $request->user();

        // Dual access logic
        if ($user) {
            // Logged-in user: Check ownership
            $customer = $order->customer;
            
            if (!$customer) {
                return response()->json([
                    'error' => 'Unauthorized access.',
                ], 403);
            }

            // Check if user owns the order via customer relationship or email match
            $isOwner = ($customer->user_id && $customer->user_id === $user->id) 
                || ($customer->email && $customer->email === $user->email);

            if (!$isOwner) {
                return response()->json([
                    'error' => 'Unauthorized access.',
                ], 403);
            }
        } else {
            // Guest: Require token
            $token = $request->query('token');

            if (!$token || $token !== $order->tracking_token) {
                return response()->json([
                    'error' => 'Invalid tracking token.',
                ], 403);
            }
        }

        return response()->json(new OrderTrackingResource($order));
    }
}

