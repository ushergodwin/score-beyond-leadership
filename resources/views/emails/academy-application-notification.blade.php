@extends('emails.layout')

@section('content')
    <h1 style="color: #a01d62; margin-top: 0;">New Academy Application</h1>
    
    <p>Hello {{ $user->name }},</p>
    
    <p>A new academy application has been submitted and requires your review.</p>
    
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
        <h2 style="color: #a01d62; margin-top: 0; font-size: 1.25rem;">Application Details</h2>
        <p style="margin: 0.5rem 0;"><strong>Application ID:</strong> #{{ $application->id }}</p>
        <p style="margin: 0.5rem 0;"><strong>Student Name:</strong> {{ $application->student_first_name }} {{ $application->student_last_name }}</p>
        <p style="margin: 0.5rem 0;"><strong>Date of Birth:</strong> {{ $application->student_date_of_birth->format('F d, Y') }}</p>
        <p style="margin: 0.5rem 0;"><strong>Age:</strong> {{ $application->student_age }} years old</p>
        <p style="margin: 0.5rem 0;"><strong>Gender:</strong> {{ ucfirst($application->student_gender) }}</p>
        <p style="margin: 0.5rem 0;"><strong>School:</strong> {{ $application->student_school ?? 'Not provided' }}</p>
        <p style="margin: 0.5rem 0;"><strong>Grade:</strong> {{ $application->student_grade ?? 'Not provided' }}</p>
        
        <h3 style="color: #a01d62; margin-top: 1.5rem; font-size: 1.1rem;">Parent/Guardian Information</h3>
        <p style="margin: 0.5rem 0;"><strong>Name:</strong> {{ $application->parent_first_name }} {{ $application->parent_last_name }}</p>
        <p style="margin: 0.5rem 0;"><strong>Email:</strong> {{ $application->parent_email }}</p>
        <p style="margin: 0.5rem 0;"><strong>Phone:</strong> {{ $application->parent_phone }}</p>
        <p style="margin: 0.5rem 0;"><strong>Relationship:</strong> {{ ucfirst($application->parent_relationship) }}</p>
        @if($application->parent_address)
        <p style="margin: 0.5rem 0;"><strong>Address:</strong> {{ $application->parent_address }}</p>
        @endif
        
        <h3 style="color: #a01d62; margin-top: 1.5rem; font-size: 1.1rem;">Emergency Contact</h3>
        <p style="margin: 0.5rem 0;"><strong>Name:</strong> {{ $application->emergency_contact_name }}</p>
        <p style="margin: 0.5rem 0;"><strong>Phone:</strong> {{ $application->emergency_contact_phone }}</p>
        @if($application->emergency_contact_relationship)
        <p style="margin: 0.5rem 0;"><strong>Relationship:</strong> {{ $application->emergency_contact_relationship }}</p>
        @endif
        
        @if($application->program_interest)
        <p style="margin: 0.5rem 0;"><strong>Program Interest:</strong> {{ ucfirst(str_replace('_', ' ', $application->program_interest)) }}</p>
        @endif
        
        @if($application->medical_conditions)
        <p style="margin: 0.5rem 0;"><strong>Medical Conditions:</strong> {{ $application->medical_conditions }}</p>
        @endif
        
        @if($application->dietary_requirements)
        <p style="margin: 0.5rem 0;"><strong>Dietary Requirements:</strong> {{ $application->dietary_requirements }}</p>
        @endif
        
        <p style="margin: 0.5rem 0;"><strong>Submitted:</strong> {{ $application->created_at->format('F d, Y h:i A') }}</p>
    </div>

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ url('/admin/academy-applications/' . $application->id) }}" class="button" style="background: linear-gradient(135deg, #a01d62 0%, #f03733 100%);">
            Review Application
        </a>
    </div>

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        This is an automated notification. Please log in to the admin panel to review and process this application.
    </p>
@endsection

