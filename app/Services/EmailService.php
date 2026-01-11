<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendDataDeletionRequestEmailJob;
use App\Jobs\SendDonationConfirmationEmailJob;
use App\Jobs\SendOrderConfirmationEmailJob;
use App\Jobs\SendOrderStatusUpdateEmailJob;
use App\Jobs\SendVolunteerApplicationConfirmationEmailJob;
use App\Jobs\SendVolunteerApplicationStatusUpdateEmailJob;
use App\Jobs\SendAcademyApplicationConfirmationEmailJob;
use App\Jobs\SendAcademyApplicationNotificationEmailJob;
use App\Mail\EmailVerificationMail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Donation;
use App\Models\Order;
use App\Models\VolunteerApplication;
use App\Models\AcademyApplication;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send order confirmation email.
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        try {
            SendOrderConfirmationEmailJob::dispatch($order);

            Log::info('Order confirmation email job dispatched', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch order confirmation email job', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send order status update email (shipped, delivered, etc.).
     */
    public function sendOrderStatusUpdate(Order $order, string $status): bool
    {
        try {
            SendOrderStatusUpdateEmailJob::dispatch($order, $status);

            Log::info('Order status update email job dispatched', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch order status update email job', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send donation confirmation email.
     */
    public function sendDonationConfirmation(Donation $donation): bool
    {
        try {
            SendDonationConfirmationEmailJob::dispatch($donation);

            Log::info('Donation confirmation email job dispatched', [
                'donation_id' => $donation->id,
                'donation_number' => $donation->donation_number,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch donation confirmation email job', [
                'donation_id' => $donation->id,
                'donation_number' => $donation->donation_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send email verification email.
     * Sends immediately (not queued) for better user experience.
     */
    public function sendEmailVerification($user, string $verificationUrl): bool
    {
        try {
            if (!$user->email) {
                Log::warning('Cannot send email verification: user has no email address', [
                    'user_id' => $user->id,
                ]);
                return false;
            }

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

            Log::info('Email verification email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send password reset email.
     * Sends immediately (not queued) for better user experience.
     */
    public function sendPasswordReset($user, string $resetUrl): bool
    {
        try {
            if (!$user->email) {
                Log::warning('Cannot send password reset email: user has no email address', [
                    'user_id' => $user->id,
                ]);
                return false;
            }

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));

            Log::info('Password reset email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send volunteer application confirmation email.
     */
    public function sendVolunteerApplicationConfirmation(VolunteerApplication $application): bool
    {
        try {
            SendVolunteerApplicationConfirmationEmailJob::dispatch($application);

            Log::info('Volunteer application confirmation email job dispatched', [
                'application_id' => $application->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch volunteer application confirmation email job', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send volunteer application status update email.
     */
    public function sendVolunteerApplicationStatusUpdate(VolunteerApplication $application, string $status): bool
    {
        try {
            SendVolunteerApplicationStatusUpdateEmailJob::dispatch($application, $status);

            Log::info('Volunteer application status update email job dispatched', [
                'application_id' => $application->id,
                'status' => $status,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch volunteer application status update email job', [
                'application_id' => $application->id,
                'status' => $status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send data deletion request email to admin.
     */
    public function sendDataDeletionRequest(string $email, ?string $reason, string $ipAddress, string $userAgent): bool
    {
        try {
            SendDataDeletionRequestEmailJob::dispatch($email, $reason, $ipAddress, $userAgent);

            Log::info('Data deletion request email job dispatched', [
                'request_email' => $email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch data deletion request email job', [
                'request_email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send academy application confirmation email to parent.
     */
    public function sendAcademyApplicationConfirmation(AcademyApplication $application): bool
    {
        try {
            SendAcademyApplicationConfirmationEmailJob::dispatch($application);

            Log::info('Academy application confirmation email job dispatched', [
                'application_id' => $application->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch academy application confirmation email job', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send academy application notification email to admins and managers.
     */
    public function sendAcademyApplicationNotification(AcademyApplication $application): bool
    {
        try {
            // Get all users with admin or manager roles
            $adminsAndManagers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'manager']);
            })->get();

            foreach ($adminsAndManagers as $user) {
                SendAcademyApplicationNotificationEmailJob::dispatch($application, $user);
            }

            Log::info('Academy application notification emails dispatched', [
                'application_id' => $application->id,
                'recipient_count' => $adminsAndManagers->count(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch academy application notification emails', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}

