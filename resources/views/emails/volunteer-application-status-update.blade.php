@extends('emails.layout')

@section('content')
    @if($status === 'approved')
        <h1 style="color: #a01d62; margin-top: 0;">Congratulations! Your Application Has Been Approved 🎉</h1>
        <p>We're excited to inform you that your volunteer application has been approved!</p>
    @elseif($status === 'rejected')
        <h1 style="color: #a01d62; margin-top: 0;">Volunteer Application Update</h1>
        <p>Thank you for your interest in volunteering with Score Beyond Leadership. We've reviewed your application.</p>
    @elseif($status === 'reviewing')
        <h1 style="color: #a01d62; margin-top: 0;">Your Application Is Under Review</h1>
        <p>Thank you for your patience. We're currently reviewing your volunteer application.</p>
    @else
        <h1 style="color: #a01d62; margin-top: 0;">Volunteer Application Update</h1>
        <p>Your volunteer application status has been updated.</p>
    @endif
    
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
        <p style="margin: 0.5rem 0;"><strong>Application ID:</strong> #{{ $application->id }}</p>
        <p style="margin: 0.5rem 0;"><strong>Name:</strong> {{ $application->first_name }} {{ $application->last_name }}</p>
        <p style="margin: 0.5rem 0;"><strong>Email:</strong> {{ $application->email }}</p>
        <p style="margin: 0.5rem 0;"><strong>Program Type:</strong> {{ ucfirst($application->program_type) }}</p>
        <p style="margin: 0.5rem 0;"><strong>Status:</strong> {{ ucfirst($status) }}</p>
    </div>

    @if($status === 'approved')
        <p>We're thrilled to have you join our team! Our team will be in touch with you shortly to discuss next steps and provide you with more information about your volunteer role.</p>
        <p>Please keep an eye on your email for further instructions and details about your volunteer program.</p>
    @elseif($status === 'rejected')
        <p>Unfortunately, we're unable to proceed with your application at this time. We appreciate your interest in volunteering with us and encourage you to apply again in the future.</p>
        <p>If you have any questions about this decision, please don't hesitate to contact us.</p>
    @elseif($status === 'reviewing')
        <p>Our team is carefully reviewing your application. We'll notify you as soon as we have an update. This process typically takes 5-7 business days.</p>
        <p>Thank you for your patience and interest in making a difference with Score Beyond Leadership.</p>
    @endif

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ url('/') }}" class="button">Visit Our Website</a>
    </div>

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        If you have any questions, please don't hesitate to contact us at 
        <a href="mailto:info@scorebeyondleadership.org" style="color: #a01d62;">info@scorebeyondleadership.org</a> or call us at +256 772 319503.
    </p>

    <p style="color: #6c7a89; font-size: 0.875rem;">
        Thank you for your commitment to making a difference in our communities!
    </p>
@endsection

