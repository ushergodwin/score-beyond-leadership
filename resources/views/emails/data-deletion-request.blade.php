@extends('emails.layout')

@section('content')
    <h1 style="color: #a01d62; margin-top: 0;">Data Deletion Request</h1>
    
    <p>A data deletion request has been received through the website.</p>
    
    <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin: 1.5rem 0;">
        <p style="margin: 0.5rem 0;"><strong>Email:</strong> {{ $email }}</p>
        <p style="margin: 0.5rem 0;"><strong>Reason:</strong> {{ $reason ?? 'Not provided' }}</p>
        <p style="margin: 0.5rem 0;"><strong>IP Address:</strong> {{ $ipAddress }}</p>
        <p style="margin: 0.5rem 0;"><strong>User Agent:</strong> {{ $userAgent }}</p>
        <p style="margin: 0.5rem 0;"><strong>Requested At:</strong> {{ now()->toDateTimeString() }}</p>
    </div>
    
    <p style="color: #6c7a89; font-size: 0.875rem;">
        Please process this request within 30 days as required by the Data Protection and Privacy Act, 2019 of Uganda.
    </p>
@endsection

