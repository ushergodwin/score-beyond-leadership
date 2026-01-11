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
        Schema::create('academy_applications', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('submitted');
            
            // Student Information
            $table->string('student_first_name');
            $table->string('student_last_name');
            $table->date('student_date_of_birth');
            $table->unsignedTinyInteger('student_age')->nullable();
            $table->string('student_gender');
            $table->string('student_school')->nullable();
            $table->string('student_grade')->nullable();
            
            // Parent/Guardian Information
            $table->string('parent_first_name');
            $table->string('parent_last_name');
            $table->string('parent_email');
            $table->string('parent_phone');
            $table->string('parent_relationship');
            $table->text('parent_address')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_relationship')->nullable();
            
            // Additional Information
            $table->text('medical_conditions')->nullable();
            $table->text('dietary_requirements')->nullable();
            
            // Program Interest
            $table->string('program_interest')->nullable();
            $table->text('previous_experience')->nullable();
            $table->text('expectations')->nullable();
            
            // Agreements
            $table->boolean('terms_agreed')->default(false);
            $table->boolean('media_consent')->default(false);
            
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
        Schema::dropIfExists('academy_applications');
    }
};
