<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('submitted');
            $table->string('program_type')->default('unpaid'); // paid or unpaid
            $table->string('preferred_name')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('country_of_residence')->nullable();
            $table->string('city')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_email')->nullable();
            $table->string('preferred_volunteer_role')->nullable();
            $table->json('preferred_roles')->nullable();
            $table->date('availability_start')->nullable();
            $table->date('availability_end')->nullable();
            $table->unsignedSmallInteger('length_of_stay_weeks')->nullable();
            $table->string('tshirt_size')->nullable();
            $table->text('skills_experience')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('dietary_requirements')->nullable();
            $table->boolean('accommodation_required')->default(false);
            $table->boolean('bringing_equipment')->default(false);
            $table->boolean('code_of_conduct_agreed')->default(false);
            $table->boolean('media_consent')->default(false);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('not_required');
            $table->string('cv_path')->nullable();
            $table->string('id_document_path')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
    }
};
