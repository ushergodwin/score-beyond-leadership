@extends('emails.layout')

@section('content')
    <h1 style="color: #a01d62; margin-top: 0;">Reset Your Password</h1>
    
    <p>We received a request to reset your password for your Score Beyond Leadership account.</p>
    
    <p>Click the button below to reset your password:</p>

    <div style="text-align: center; margin: 2rem 0;">
        <a href="{{ $resetUrl }}" class="button">Reset Password</a>
    </div>

    <p style="color: #6c7a89; font-size: 0.875rem;">
        If the button doesn't work, you can copy and paste this link into your browser:
    </p>
    <p style="color: #6c7a89; font-size: 0.875rem; word-break: break-all;">
        <a href="{{ $resetUrl }}" style="color: #a01d62;">{{ $resetUrl }}</a>
    </p>

    <p style="color: #6c7a89; font-size: 0.875rem; margin-top: 2rem;">
        This password reset link will expire in 60 minutes. If you didn't request a password reset, you can safely ignore this email and your password will remain unchanged.
    </p>

    <p style="color: #6c7a89; font-size: 0.875rem;">
        For security reasons, please do not share this link with anyone.
    </p>
@endsection

