<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates dealers table for customers who can place orders.
     */
    public function up(): void
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile', 15)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('gstin', 15)->nullable();
            $table->text('address')->nullable();
            $table->foreignId('zone_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('state_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pincode', 10)->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('zone_id');
            $table->index('state_id');
            $table->index('city_id');
            $table->index('mobile');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};

