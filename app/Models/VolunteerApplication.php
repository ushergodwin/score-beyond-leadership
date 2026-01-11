<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VolunteerApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
        'program_type',
        'preferred_name',
        'first_name',
        'last_name',
        'date_of_birth',
        'nationality',
        'passport_number',
        'email',
        'phone',
        'country_of_residence',
        'city',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_email',
        'preferred_volunteer_role',
        'preferred_roles',
        'availability_start',
        'availability_end',
        'length_of_stay_weeks',
        'tshirt_size',
        'skills_experience',
        'medical_conditions',
        'dietary_requirements',
        'accommodation_required',
        'bringing_equipment',
        'code_of_conduct_agreed',
        'media_consent',
        'payment_method',
        'payment_status',
        'cv_path',
        'id_document_path',
        'meta',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'availability_start' => 'date',
        'availability_end' => 'date',
        'preferred_roles' => 'array',
        'accommodation_required' => 'boolean',
        'bringing_equipment' => 'boolean',
        'code_of_conduct_agreed' => 'boolean',
        'media_consent' => 'boolean',
        'meta' => 'array',
    ];
}

