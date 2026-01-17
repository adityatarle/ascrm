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
        Schema::table('discount_slabs', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable()->after('organization_id'); // e.g., "Standard Slab", "Premium Slab"
            $table->text('description')->nullable()->after('name');
            
            $table->index('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_slabs', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'name', 'description']);
        });
    }
};
