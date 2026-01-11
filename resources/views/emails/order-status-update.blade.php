@extends('emails.layout')

@section('content')
    @if($status === 'shipped')
        <h1 style="color: #a01d62; margin-top: 0;">Your Order Has Been Shipped! 🚚</h1>
        <p>Great news! Your order has been shipped and is on its way to you.</p>
    @elseif($status === 'delivered')
        <h1 style="color: #a01d62; margin-top: 0;">Your Order Has Been Delivered! 📦</h1>
        <p>Your order has been successfully delivered. We hope you love your purchase!</p>
    @else
        <h1 style="color: #a01d62; margin-top: 0;">Order Status Update</h1>
        <p>Your order status has been updated.</p>
    @endif
    
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
        <p style="margin: 0.5rem 0;"><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p style="margin: 0.5rem 0;"><strong>Status:</strong> {{ ucfirst($status) }}</p>
        <p style="margin: 0.5rem 0;"><strong>Total Amount:</strong> {{ number_format($order->grand_total, 2) }} {{ $order->currency }}</p>
    </div>

    @if($status === 'shipped')
        <p>You can track your order using the link below. If you have any questions or concerns, please don't hesitate to contact us.</p>
    @elseif($status === 'delivered')
        <p>We'd love to hear about your experience! If you have any feedback or questions, please reach out to us.</p>
    @endif

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ route('dashboard.orders.show', $order->order_number) }}" class="button">View Order Details</a>
    </div>

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

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        Thank you for shopping with Score Beyond Leadership!
    </p>
@endsection

