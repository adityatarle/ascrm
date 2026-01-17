<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates product_state_rates table for state-specific pricing.
     */
    public function up(): void
    {
        Schema::create('product_state_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->decimal('rate', 10, 2);
            $table->timestamps();

            $table->unique(['product_id', 'state_id']);
            $table->index('product_id');
            $table->index('state_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_state_rates');
    }
};

