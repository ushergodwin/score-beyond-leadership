<?php

namespace App\Jobs;

use App\Mail\DonationConfirmationMail;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDonationConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Donation $donation,
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $email = $this->donation->email;

            if (!$email) {
                Log::warning('Cannot send donation confirmation email: no email address found', [
                    'donation_id' => $this->donation->id,
                    'donation_number' => $this->donation->donation_number,
                ]);
                return;
            }

            Mail::to($email)->send(new DonationConfirmationMail($this->donation));

            Log::info('Donation confirmation email sent', [
                'donation_id' => $this->donation->id,
                'donation_number' => $this->donation->donation_number,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send donation confirmation email', [
                'donation_id' => $this->donation->id,
                'donation_number' => $this->donation->donation_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

