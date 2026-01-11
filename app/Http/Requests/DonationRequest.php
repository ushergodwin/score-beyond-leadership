<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currency = $this->input('currency', 'UGX');
        $minAmount = match ($currency) {
            'UGX' => 1000,
            'USD' => 1,
            'EUR' => 1, // AURO is EUR
            default => 1000,
        };

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'size:2'],
            'organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'amount' => ['required', 'numeric', "min:{$minAmount}"],
            'currency' => ['required', 'string', 'in:UGX,USD,EUR'],
            'is_recurring' => ['nullable', 'boolean'],
            'frequency' => ['nullable', 'string', Rule::in(['one-time', 'DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY', 'monthly', 'quarterly', 'yearly'])],
            'impact_tier' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'string', Rule::in(['pesapal_visa_mastercard', 'pesapal_mobile_money'])],
            'tax_receipt_requested' => ['nullable', 'boolean'],
            'consent_to_contact' => ['nullable', 'boolean'],
            'communications_opt_in' => ['nullable', 'boolean'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        $nullableFields = ['phone', 'country', 'organization', 'address', 'frequency', 'impact_tier', 'message'];
        foreach ($nullableFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }

        // Convert string booleans to actual booleans for checkbox fields
        $this->merge([
            'is_recurring' => filter_var($this->is_recurring ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'tax_receipt_requested' => filter_var($this->tax_receipt_requested ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'consent_to_contact' => filter_var($this->consent_to_contact ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'communications_opt_in' => filter_var($this->communications_opt_in ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);

        // Handle frequency: if it's 'one-time', set is_recurring to false
        if ($this->frequency === 'one-time') {
            $this->merge([
                'is_recurring' => false,
            ]);
        }
    }
}

