<?php

namespace App\Jobs;

use App\Mail\AcademyApplicationNotificationMail;
use App\Models\AcademyApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAcademyApplicationNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AcademyApplication $application,
        public User $user,
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $email = $this->user->email;

            if (!$email) {
                Log::warning('Cannot send academy application notification email: user has no email address', [
                    'application_id' => $this->application->id,
                    'user_id' => $this->user->id,
                ]);
                return;
            }

            Mail::to($email)->send(new AcademyApplicationNotificationMail($this->application, $this->user));

            Log::info('Academy application notification email sent', [
                'application_id' => $this->application->id,
                'user_id' => $this->user->id,
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send academy application notification email', [
                'application_id' => $this->application->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
