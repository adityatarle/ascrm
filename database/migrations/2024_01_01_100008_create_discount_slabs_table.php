<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates discount_slabs table for order value-based discounts.
     */
    public function up(): void
    {
        Schema::create('discount_slabs', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_amount', 10, 2);
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('min_amount');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_slabs');
    }
};

