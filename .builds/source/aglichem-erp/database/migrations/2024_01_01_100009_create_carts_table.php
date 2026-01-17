<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates carts table for shopping cart functionality.
     * Supports both dealer-based (logged in) and session-based (guest) carts.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->index('dealer_id');
            $table->index('session_id');
            $table->index('product_id');
            $table->unique(['dealer_id', 'product_id'], 'dealer_product_unique');
            $table->unique(['session_id', 'product_id'], 'session_product_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};

