<?php

namespace App\Filament\Resources\DonationResource\Pages;

use App\Filament\Resources\DonationResource;
use App\Services\EmailService;
use App\Services\NotificationService;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\EditRecord;

class EditDonation extends EditRecord
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\ViewAction::make(),
            FilamentActions\DeleteAction::make(),
        ];
    }

    protected $originalPaymentStatus;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Capture original values before save
        $this->originalPaymentStatus = $this->record->payment_status;

        return $data;
    }

    protected function afterSave(): void
    {
        $donation = $this->record;
        $donation->refresh();

        $newPaymentStatus = $donation->payment_status;

        $notificationService = app(NotificationService::class);
        $emailService = app(EmailService::class);

        // Handle payment status changes
        if ($this->originalPaymentStatus !== $newPaymentStatus) {
            $notificationService->notifyDonationStatus($donation, $newPaymentStatus);

            // Send email for completed donations
            if ($newPaymentStatus === 'completed') {
                $emailService->sendDonationConfirmation($donation);
            }
        }
    }
}

