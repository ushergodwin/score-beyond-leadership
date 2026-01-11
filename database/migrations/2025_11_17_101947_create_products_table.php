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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('status')->default('draft');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('care_instructions')->nullable();
            $table->text('materials')->nullable();
            $table->text('artisan_story')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->string('base_currency', 3)->default('UGX');
            $table->boolean('is_limited_edition')->default(false);
            $table->string('limited_badge_label')->nullable();
            $table->unsignedInteger('inventory')->default(0);
            $table->json('meta')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
