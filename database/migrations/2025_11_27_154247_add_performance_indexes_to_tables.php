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
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            // customer_id already has index from foreign key
            $table->index('status');
            $table->index('payment_status');
            $table->index('placed_at');
            $table->index(['customer_id', 'status']); // Composite for common queries
        });

        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            // slug already has unique index
            // sku already has unique index
            $table->index('status');
            $table->index('published_at');
            $table->index('is_featured');
            $table->index(['status', 'published_at']); // Composite for published products
        });

        // Donations table indexes
        Schema::table('donations', function (Blueprint $table) {
            // donation_number already has unique index
            // customer_id already has index from foreign key
            $table->index('email');
            $table->index('payment_status');
            $table->index('paid_at');
            $table->index(['email', 'payment_status']); // Composite for user donations
        });

        // Volunteer applications table indexes
        Schema::table('volunteer_applications', function (Blueprint $table) {
            $table->index('email');
            $table->index('status');
            $table->index('program_type');
            $table->index(['status', 'program_type']); // Composite for filtering
        });

        // Academy applications table indexes
        Schema::table('academy_applications', function (Blueprint $table) {
            $table->index('parent_email');
            $table->index('status');
            $table->index('program_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['placed_at']);
            $table->dropIndex(['customer_id', 'status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['status', 'published_at']);
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['paid_at']);
            $table->dropIndex(['email', 'payment_status']);
        });

        Schema::table('volunteer_applications', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['status']);
            $table->dropIndex(['program_type']);
            $table->dropIndex(['status', 'program_type']);
        });

        Schema::table('academy_applications', function (Blueprint $table) {
            $table->dropIndex(['parent_email']);
            $table->dropIndex(['status']);
            $table->dropIndex(['program_interest']);
        });
    }
};
