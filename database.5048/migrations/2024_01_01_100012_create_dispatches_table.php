<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates dispatches table for order dispatch/shipment tracking.
     */
    public function up(): void
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('dispatch_number')->unique();
            $table->string('lr_number')->nullable();
            $table->string('transporter_name')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('order_id');
            $table->index('dispatch_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};

