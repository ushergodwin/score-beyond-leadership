<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Services\EmailService;

class PolicyController extends Controller
{
    /**
     * Display the Privacy Policy page
     */
    public function privacy()
    {
        return Inertia::render('Policies/Privacy');
    }

    /**
     * Display the Terms of Service page
     */
    public function terms()
    {
        return Inertia::render('Policies/Terms');
    }

    /**
     * Display the Refund Policy page
     */
    public function refund()
    {
        return Inertia::render('Policies/Refund');
    }

    /**
     * Handle data deletion request
     */
    public function requestDataDeletion(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'reason' => 'nullable|string|max:1000',
            'confirmation' => 'required|accepted',
        ]);

        // Log the request
        Log::info('Data deletion request received', [
            'email' => $validated['email'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send email notification to admin
        app(EmailService::class)->sendDataDeletionRequest(
            email: $validated['email'],
            reason: $validated['reason'] ?? null,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return back()->with('success', 'Your data deletion request has been received. We will process it within 30 days and contact you at the provided email address.');
    }
}

