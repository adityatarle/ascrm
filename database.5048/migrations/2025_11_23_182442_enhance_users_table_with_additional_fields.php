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
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('mobile');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('pincode', 10)->nullable()->after('state');
            $table->string('phone', 20)->nullable()->after('pincode');
            $table->string('alternate_email')->nullable()->after('phone');
            $table->string('emergency_contact_name', 100)->nullable()->after('alternate_email');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            $table->text('notes')->nullable()->after('emergency_contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'address',
                'city',
                'state',
                'pincode',
                'phone',
                'alternate_email',
                'emergency_contact_name',
                'emergency_contact_phone',
                'notes',
            ]);
        });
    }
};
