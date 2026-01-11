@extends('emails.layout')

@section('content')
    <h1 style="color: #a01d62; margin-top: 0;">Verify Your Email Address</h1>
    
    <p>Thank you for registering with Score Beyond Leadership! Please verify your email address to complete your account setup.</p>
    
    <p>Click the button below to verify your email address:</p>

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
    </div>

    <p style="color: #6c7a89; font-size: 0.875rem;">
        If the button doesn't work, you can copy and paste this link into your browser:
    </p>
    <p style="color: #6c7a89; font-size: 0.875rem; word-break: break-all;">
        <a href="{{ $verificationUrl }}" style="color: #a01d62;">{{ $verificationUrl }}</a>
    </p>

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        This verification link will expire in 60 minutes. If you didn't create an account, you can safely ignore this email.
    </p>
@endsection

