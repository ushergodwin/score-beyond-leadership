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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->string('provider'); // pesapal, paypal, etc.
            $table->string('provider_reference')->nullable();
            $table->string('status')->default('pending');
            $table->string('currency', 3)->default('UGX');
            $table->decimal('amount', 12, 2);
            $table->string('pesapal_tracking_id')->nullable();
            $table->string('pesapal_merchant_reference')->nullable();
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['provider', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
