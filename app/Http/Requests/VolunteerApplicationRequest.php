<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VolunteerApplicationRequest extends FormRequest
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
        $isPaidProgram = $this->input('program_type') === 'paid';
        $isDiaspora = $this->input('country_of_residence') !== 'UG';

        return [
            // Personal Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'passport_number' => [
                $isDiaspora ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'country_of_residence' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],

            // Emergency Contact
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:255'],
            'emergency_contact_email' => ['nullable', 'email', 'max:255'],

            // Program Details
            'program_type' => ['required', 'string', Rule::in(['paid', 'unpaid'])],
            'preferred_volunteer_role' => ['nullable', 'string', 'max:255'],
            'preferred_roles' => ['nullable', 'array'],
            'preferred_roles.*' => ['string', 'max:255'],
            'availability_start' => ['nullable', 'date', 'after_or_equal:today'],
            'availability_end' => ['nullable', 'date', 'after:availability_start'],
            'length_of_stay_weeks' => ['nullable', 'integer', 'min:1', 'max:104'],

            // Additional Information
            'tshirt_size' => ['nullable', 'string', Rule::in(['XS', 'S', 'M', 'L', 'XL', 'XXL'])],
            'skills_experience' => ['nullable', 'string', 'max:2000'],
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'dietary_requirements' => ['nullable', 'string', 'max:500'],
            'accommodation_required' => ['boolean'],
            'bringing_equipment' => ['boolean'],

            // Agreements
            'code_of_conduct_agreed' => ['required', 'accepted'],
            'media_consent' => ['boolean'],

            // Payment (conditional)
            'payment_method' => [
                $isPaidProgram ? 'required' : 'nullable',
                'string',
                Rule::in(['pesapal_visa_mastercard', 'pesapal_mobile_money', 'bank_transfer']),
            ],

            // File Uploads
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'passport_number.required' => 'Passport number is required for diaspora volunteers.',
            'code_of_conduct_agreed.required' => 'You must agree to the code of conduct to submit your application.',
            'code_of_conduct_agreed.accepted' => 'You must agree to the code of conduct to submit your application.',
            'payment_method.required' => 'Payment method is required for paid programs.',
            'cv.max' => 'CV file size must not exceed 5MB.',
            'id_document.max' => 'ID document file size must not exceed 5MB.',
        ];
    }
}

