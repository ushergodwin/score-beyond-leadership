<?php

namespace App\Filament\Resources\VolunteerApplicationResource\Pages;

use App\Filament\Resources\VolunteerApplicationResource;
use App\Services\EmailService;
use App\Services\NotificationService;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerApplication extends EditRecord
{
    protected static string $resource = VolunteerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\ViewAction::make(),
            FilamentActions\DeleteAction::make(),
        ];
    }

    protected $originalStatus;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Capture original values before save
        $this->originalStatus = $this->record->status;

        return $data;
    }

    protected function afterSave(): void
    {
        $application = $this->record;
        $application->refresh();

        $newStatus = $application->status;

        $notificationService = app(NotificationService::class);
        $emailService = app(EmailService::class);

        // Handle status changes (only send for meaningful status changes)
        if ($this->originalStatus !== $newStatus && in_array($newStatus, ['approved', 'rejected', 'reviewing'])) {
            // Send email notification
            $emailService->sendVolunteerApplicationStatusUpdate($application, $newStatus);

            // Create in-app notification (only for users with accounts)
            $notificationService->notifyVolunteerApplicationStatus($application, $newStatus);
        }
    }
}

