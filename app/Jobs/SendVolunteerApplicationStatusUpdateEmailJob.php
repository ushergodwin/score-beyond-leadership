<?php

namespace App\Jobs;

use App\Mail\VolunteerApplicationStatusUpdateMail;
use App\Models\VolunteerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVolunteerApplicationStatusUpdateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public VolunteerApplication $application,
        public string $status,
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $email = $this->application->email;

            if (!$email) {
                Log::warning('Cannot send volunteer application status update email: no email address found', [
                    'application_id' => $this->application->id,
                    'status' => $this->status,
                ]);
                return;
            }

            Mail::to($email)->send(new VolunteerApplicationStatusUpdateMail($this->application, $this->status));

            Log::info('Volunteer application status update email sent', [
                'application_id' => $this->application->id,
                'status' => $this->status,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send volunteer application status update email', [
                'application_id' => $this->application->id,
                'status' => $this->status,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

