@extends('emails.layout')

@section('content')
    <h1 style="color: #a01d62; margin-top: 0;">Order Confirmation</h1>
    
    <p>Thank you for your order! We've received your order and payment has been confirmed.</p>
    
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
        <p style="margin: 0.5rem 0;"><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p style="margin: 0.5rem 0;"><strong>Order Date:</strong> {{ $order->placed_at?->format('F d, Y h:i A') ?? $order->created_at->format('F d, Y h:i A') }}</p>
        <p style="margin: 0.5rem 0;"><strong>Total Amount:</strong> {{ number_format($order->grand_total, 2) }} {{ $order->currency }}</p>
    </div>

    <h2 style="color: #a01d62; margin-top: 2rem;">Order Items</h2>
    <table style="width: 100%; border-collapse: collapse; margin: 1rem 0;">
        <thead>
            <tr style="background-color: #f8f9fa;">
                <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #e5e7eb;">Item</th>
                <th style="padding: 0.75rem; text-align: right; border-bottom: 2px solid #e5e7eb;">Quantity</th>
                <th style="padding: 0.75rem; text-align: right; border-bottom: 2px solid #e5e7eb;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">{{ $item->product_name }}</td>
                <td style="padding: 0.75rem; text-align: right; border-bottom: 1px solid #e5e7eb;">{{ $item->quantity }}</td>
                <td style="padding: 0.75rem; text-align: right; border-bottom: 1px solid #e5e7eb;">{{ number_format($item->unit_price, 2) }} {{ $order->currency }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="padding: 0.75rem; text-align: right; font-weight: bold;">Subtotal:</td>
                <td style="padding: 0.75rem; text-align: right; font-weight: bold;">{{ number_format($order->subtotal, 2) }} {{ $order->currency }}</td>
            </tr>
            @if($order->shipping_total > 0)
            <tr>
                <td colspan="2" style="padding: 0.75rem; text-align: right;">Shipping:</td>
                <td style="padding: 0.75rem; text-align: right;">{{ number_format($order->shipping_total, 2) }} {{ $order->currency }}</td>
            </tr>
            @endif
            <tr style="background-color: #f8f9fa;">
                <td colspan="2" style="padding: 0.75rem; text-align: right; font-weight: bold; font-size: 1.1rem;">Total:</td>
                <td style="padding: 0.75rem; text-align: right; font-weight: bold; font-size: 1.1rem;">{{ number_format($order->grand_total, 2) }} {{ $order->currency }}</td>
            </tr>
        </tfoot>
    </table>

    @if($order->shippingAddress)
    <h2 style="color: #a01d62; margin-top: 2rem;">Shipping Address</h2>
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1rem 0;">
        <p style="margin: 0.25rem 0;">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
        <p style="margin: 0.25rem 0;">{{ $order->shippingAddress->line_one }}</p>
        @if($order->shippingAddress->line_two)
        <p style="margin: 0.25rem 0;">{{ $order->shippingAddress->line_two }}</p>
        @endif
        <p style="margin: 0.25rem 0;">{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}</p>
        <p style="margin: 0.25rem 0;">{{ $order->shippingAddress->postal_code }}</p>
        <p style="margin: 0.25rem 0;">{{ $order->shippingAddress->country }}</p>
    </div>
    @endif

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ route('dashboard.orders.show', $order->order_number) }}" class="button">View Order Details</a>
    </div>

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        We're preparing your order for shipment. You'll receive another email when your order ships.
    </p>
@endsection

