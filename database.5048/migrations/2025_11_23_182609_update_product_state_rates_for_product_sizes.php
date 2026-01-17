<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First drop the existing unique constraint
        Schema::table('product_state_rates', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'state_id']);
        });
        
        // Add product_size_id column
        Schema::table('product_state_rates', function (Blueprint $table) {
            $table->foreignId('product_size_id')->nullable()->after('product_id')->constrained()->onDelete('cascade');
            $table->index('product_size_id');
        });
        
        // Add unique constraint - MySQL treats NULL as distinct, so this works
        // Base product rates: product_id + NULL + state_id must be unique
        // Size-specific rates: product_id + product_size_id + state_id must be unique
        DB::statement('ALTER TABLE product_state_rates ADD UNIQUE KEY product_state_rate_unique (product_id, product_size_id, state_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_state_rates', function (Blueprint $table) {
            $table->dropUnique('product_state_rate_unique');
            $table->dropForeign(['product_size_id']);
            $table->dropColumn('product_size_id');
            
            // Restore original unique constraint
            $table->unique(['product_id', 'state_id']);
        });
    }
};
