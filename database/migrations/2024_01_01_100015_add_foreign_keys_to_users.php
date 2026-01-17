<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add foreign key constraint to users table after organizations table is created.
     */
    public function up(): void
    {
        // Check if foreign key already exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = 'organization_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [config('database.connections.mysql.database')]);
        
        if (empty($foreignKeys)) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('organization_id')
                      ->references('id')
                      ->on('organizations')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });
    }
};

