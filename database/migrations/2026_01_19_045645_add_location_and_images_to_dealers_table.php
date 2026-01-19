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
        Schema::table('dealers', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable()->after('state_id');
            $table->unsignedBigInteger('taluka_id')->nullable()->after('district_id');
            $table->string('image_1')->nullable()->after('notes');
            $table->string('image_2')->nullable()->after('image_1');
            $table->string('image_3')->nullable()->after('image_2');
            $table->string('image_4')->nullable()->after('image_3');

            // Add foreign key constraints if the tables exist
            // Note: These reference tbl_dist_master and tbl_taluka_master
            // We'll add indexes but not foreign keys since they use different primary key names
            $table->index('district_id');
            $table->index('taluka_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropIndex(['district_id']);
            $table->dropIndex(['taluka_id']);
            $table->dropColumn(['district_id', 'taluka_id', 'image_1', 'image_2', 'image_3', 'image_4']);
        });
    }
};
