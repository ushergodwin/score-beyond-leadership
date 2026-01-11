<?php

namespace App\Jobs;

use App\Mail\DataDeletionRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDataDeletionRequestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email,
        public ?string $reason,
        public string $ipAddress,
        public string $userAgent,
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $adminEmail = config('mail.from.address');

            if (!$adminEmail) {
                Log::warning('Cannot send data deletion request email: admin email not configured');
                return;
            }

            Mail::to($adminEmail)->send(new DataDeletionRequestMail(
                $this->email,
                $this->reason,
                $this->ipAddress,
                $this->userAgent
            ));

            Log::info('Data deletion request email sent', [
                'request_email' => $this->email,
                'admin_email' => $adminEmail,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send data deletion request email', [
                'request_email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

