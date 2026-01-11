<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademyApplicationRequest extends FormRequest
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
        return [
            // Student Information
            'student_first_name' => ['required', 'string', 'max:255'],
            'student_last_name' => ['required', 'string', 'max:255'],
            'student_date_of_birth' => ['required', 'date', 'before:today'],
            'student_age' => ['nullable', 'integer', 'min:6', 'max:18'],
            'student_gender' => ['required', 'string', 'in:male,female,other'],
            'student_school' => ['nullable', 'string', 'max:255'],
            'student_grade' => ['nullable', 'string', 'max:255'],

            // Parent/Guardian Information
            'parent_first_name' => ['required', 'string', 'max:255'],
            'parent_last_name' => ['required', 'string', 'max:255'],
            'parent_email' => ['required', 'email', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:255'],
            'parent_relationship' => ['required', 'string', 'in:parent,guardian,other'],
            'parent_address' => ['nullable', 'string', 'max:500'],

            // Emergency Contact
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:255'],

            // Additional Information
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'dietary_requirements' => ['nullable', 'string', 'max:500'],

            // Program Interest
            'program_interest' => ['nullable', 'string', 'in:sports_camps,personal_growth,volunteer_trips,all'],
            'previous_experience' => ['nullable', 'string', 'max:2000'],
            'expectations' => ['nullable', 'string', 'max:2000'],

            // Agreements
            'terms_agreed' => ['required', 'accepted'],
            'media_consent' => ['boolean'],
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
            'student_date_of_birth.before' => 'Date of birth must be in the past.',
            'student_age.min' => 'Student must be at least 6 years old.',
            'student_age.max' => 'Student must be 18 years or younger.',
            'terms_agreed.required' => 'You must agree to the terms and conditions to submit your application.',
            'terms_agreed.accepted' => 'You must agree to the terms and conditions to submit your application.',
        ];
    }
}
