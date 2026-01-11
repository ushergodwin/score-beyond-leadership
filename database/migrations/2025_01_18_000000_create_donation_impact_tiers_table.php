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
        Schema::create('donation_impact_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('label'); // e.g., "Supporter", "Champion", "Leader", "Hero"
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3); // UGX, USD, EUR
            $table->text('description');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_impact_tiers');
    }
};

