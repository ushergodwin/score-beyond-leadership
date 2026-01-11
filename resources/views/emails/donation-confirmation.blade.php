@extends('emails.layout')

@section('content')
    <h1 style="color: #a01d62; margin-top: 0;">Thank You for Your Donation!</h1>
    
    <p>We are incredibly grateful for your generous donation to Score Beyond Leadership. Your support helps us transform lives through sports development and leadership programs.</p>
    
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
        <p style="margin: 0.5rem 0;"><strong>Donation Number:</strong> {{ $donation->donation_number }}</p>
        <p style="margin: 0.5rem 0;"><strong>Donation Date:</strong> {{ $donation->paid_at?->format('F d, Y h:i A') ?? $donation->created_at->format('F d, Y h:i A') }}</p>
        <p style="margin: 0.5rem 0;"><strong>Amount:</strong> {{ number_format($donation->amount, 2) }} {{ $donation->currency }}</p>
        @if($donation->is_recurring)
        <p style="margin: 0.5rem 0;"><strong>Recurring:</strong> Yes ({{ ucfirst($donation->frequency) }})</p>
        @endif
        @if($donation->impact_tier)
        <p style="margin: 0.5rem 0;"><strong>Impact Tier:</strong> {{ $donation->impact_tier }}</p>
        @endif
    </div>

    @if($donation->message)
    <div style="background-color: #fef5e8; padding: 1.5rem; border-left: 4px solid #f89f3d; border-radius: 0.5rem; margin: 1.5rem 0;">
        <p style="margin: 0; font-style: italic;">"{{ $donation->message }}"</p>
    </div>
    @endif

    <p>Your donation will directly support our programs in:</p>
    <ul>
        <li><strong>Education:</strong> Providing educational opportunities and resources</li>
        <li><strong>Livelihood:</strong> Creating sustainable income-generating activities</li>
        <li><strong>Health & Wellbeing:</strong> Promoting physical and mental health</li>
    </ul>

    @if($donation->tax_receipt_requested)
    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 1.5rem;">
        A tax receipt will be sent to your email address shortly.
    </p>
    @endif

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ route('dashboard.donations.show', $donation->donation_number) }}" class="button">View Donation Details</a>
    </div>

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        Thank you for being part of our mission to empower communities and transform lives.
    </p>
@endsection

