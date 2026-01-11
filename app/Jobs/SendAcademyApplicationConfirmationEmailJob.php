<?php

namespace App\Jobs;

use App\Mail\AcademyApplicationConfirmationMail;
use App\Models\AcademyApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAcademyApplicationConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AcademyApplication $application,
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $email = $this->application->parent_email;

            if (!$email) {
                Log::warning('Cannot send academy application confirmation email: no email address found', [
                    'application_id' => $this->application->id,
                ]);
                return;
            }

            Mail::to($email)->send(new AcademyApplicationConfirmationMail($this->application));

            Log::info('Academy application confirmation email sent', [
                'application_id' => $this->application->id,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send academy application confirmation email', [
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
