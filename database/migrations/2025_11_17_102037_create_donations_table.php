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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donation_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('organization')->nullable();
            $table->string('address')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('UGX');
            $table->decimal('exchange_rate', 12, 6)->default(1);
            $table->boolean('is_recurring')->default(false);
            $table->string('frequency')->nullable(); // monthly, quarterly
            $table->string('impact_tier')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('pesapal_tracking_id')->nullable();
            $table->boolean('tax_receipt_requested')->default(false);
            $table->string('tax_receipt_path')->nullable();
            $table->boolean('consent_to_contact')->default(false);
            $table->boolean('communications_opt_in')->default(false);
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
