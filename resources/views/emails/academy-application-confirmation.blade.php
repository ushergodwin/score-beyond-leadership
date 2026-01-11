@extends('emails.layout')

@section('content')
    <h1 style="color: #a01d62; margin-top: 0;">Academy Application Received</h1>
    
    <p>Thank you for your interest in Score Beyond Academy! We've successfully received your application for {{ $application->student_first_name }} {{ $application->student_last_name }}.</p>
    
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
        <p style="margin: 0.5rem 0;"><strong>Application ID:</strong> #{{ $application->id }}</p>
        <p style="margin: 0.5rem 0;"><strong>Student Name:</strong> {{ $application->student_first_name }} {{ $application->student_last_name }}</p>
        <p style="margin: 0.5rem 0;"><strong>Age:</strong> {{ $application->student_age }} years old</p>
        <p style="margin: 0.5rem 0;"><strong>Parent/Guardian:</strong> {{ $application->parent_first_name }} {{ $application->parent_last_name }}</p>
        <p style="margin: 0.5rem 0;"><strong>Email:</strong> {{ $application->parent_email }}</p>
        <p style="margin: 0.5rem 0;"><strong>Phone:</strong> {{ $application->parent_phone }}</p>
        <p style="margin: 0.5rem 0;"><strong>Submitted:</strong> {{ $application->created_at->format('F d, Y h:i A') }}</p>
    </div>

    <h2 style="color: #a01d62; margin-top: 2rem;">What Happens Next?</h2>
    <p>Our team will review your application and get back to you within 5-7 business days. We'll contact you at the email address you provided.</p>

    <p>In the meantime, you can learn more about our academy programs and impact by visiting our website.</p>

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ url('/') }}" class="button">Visit Our Website</a>
    </div>

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        If you have any questions about your application, please don't hesitate to contact us at 
        <a href="mailto:info@scorebeyondleadership.org" style="color: #a01d62;">info@scorebeyondleadership.org</a> or call us at +256 772 319503.
    </p>

    <p style="color: #6c7a89; font-size: 0.875rem;">
        Thank you for choosing Score Beyond Academy!
    </p>
@endsection

