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
        // Add soft deletes to all main tables
        $tables = [
            'users',
            'organizations',
            'dealers',
            'products',
            'product_state_rates',
            'product_sizes',
            'countries',
            'states',
            'regions',
            'zones',
            'cities',
            'discount_slabs',
            'carts',
            'orders',
            'order_items',
            'dispatches',
            'payments',
            'units',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'organizations',
            'dealers',
            'products',
            'product_state_rates',
            'product_sizes',
            'countries',
            'states',
            'regions',
            'zones',
            'cities',
            'discount_slabs',
            'carts',
            'orders',
            'order_items',
            'dispatches',
            'payments',
            'units',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
