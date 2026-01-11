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
        Schema::create('volunteer_programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // 'paid' or 'unpaid'
            $table->string('title');
            $table->string('badge');
            $table->text('summary');
            $table->text('description')->nullable();
            $table->json('benefits')->nullable(); // Array of benefit strings
            $table->json('logistics')->nullable(); // Array of logistics strings
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_programs');
    }
};
