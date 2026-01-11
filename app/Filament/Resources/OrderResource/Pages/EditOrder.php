<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Services\EmailService;
use App\Services\NotificationService;
use Filament\Actions as FilamentActions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\ViewAction::make(),
            FilamentActions\DeleteAction::make(),
        ];
    }

    protected $originalStatus;
    protected $originalPaymentStatus;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Capture original values before save
        $this->originalStatus = $this->record->status;
        $this->originalPaymentStatus = $this->record->payment_status;

        return $data;
    }

    protected function afterSave(): void
    {
        $order = $this->record;
        $order->refresh();
        
        // Load relationships safely
        $order->load(['customer', 'items', 'shippingAddress', 'billingAddress']);
        if ($order->customer) {
            $order->customer->load('user');
        }

        $newStatus = $order->status;
        $newPaymentStatus = $order->payment_status;

        $notificationService = app(NotificationService::class);
        $emailService = app(EmailService::class);

        // Handle payment status changes
        if ($this->originalPaymentStatus !== $newPaymentStatus) {
            $notificationService->notifyOrderStatus($order, $newPaymentStatus);

            // Send email for completed payments
            if ($newPaymentStatus === 'completed') {
                $emailService->sendOrderConfirmation($order);
            }
        }

        // Handle order status changes (shipped, delivered, etc.)
        if ($this->originalStatus !== $newStatus) {
            // Send email for status updates
            if (in_array($newStatus, ['shipped', 'delivered', 'processing'])) {
                $emailService->sendOrderStatusUpdate($order, $newStatus);
            }

            // Create notification for order status changes (only for users with accounts)
            // The notifyOrderStatus method handles both payment_status and order status
            if (in_array($newStatus, ['processing', 'shipped', 'delivered'])) {
                $notificationService->notifyOrderStatus($order, $newStatus);
            }
        }
    }
}

