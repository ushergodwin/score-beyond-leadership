<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademyApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
        'student_first_name',
        'student_last_name',
        'student_date_of_birth',
        'student_age',
        'student_gender',
        'student_school',
        'student_grade',
        'parent_first_name',
        'parent_last_name',
        'parent_email',
        'parent_phone',
        'parent_relationship',
        'parent_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'medical_conditions',
        'dietary_requirements',
        'program_interest',
        'previous_experience',
        'expectations',
        'terms_agreed',
        'media_consent',
        'meta',
    ];

    protected $casts = [
        'student_date_of_birth' => 'date',
        'student_age' => 'integer',
        'terms_agreed' => 'boolean',
        'media_consent' => 'boolean',
        'meta' => 'array',
    ];
}
