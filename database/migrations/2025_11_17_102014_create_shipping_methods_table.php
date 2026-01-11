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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type')->default('local'); // local, international, pickup
            $table->string('region')->nullable();
            $table->string('carrier')->nullable();
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->string('currency', 3)->default('UGX');
            $table->unsignedSmallInteger('estimated_min_days')->nullable();
            $table->unsignedSmallInteger('estimated_max_days')->nullable();
            $table->boolean('is_pickup')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
