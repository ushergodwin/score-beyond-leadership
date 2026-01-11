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
        Schema::create('academy_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->default('academy'); // Single page, but using slug for consistency
            $table->string('hero_title');
            $table->text('hero_subtitle');
            $table->string('offers_heading')->nullable();
            $table->text('offers_description');
            $table->json('offerings')->nullable(); // Array of {icon, label} objects
            $table->string('location')->nullable();
            $table->string('why_matters_heading')->nullable();
            $table->text('why_matters_description');
            $table->string('join_heading')->nullable();
            $table->text('join_description');
            $table->string('join_cta_text')->default('Apply Now');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academy_pages');
    }
};
