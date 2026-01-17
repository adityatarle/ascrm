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
            $table->date('date_of_birth')->nullable()->after('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('alternate_mobile', 15)->nullable()->after('mobile');
            $table->string('phone', 20)->nullable()->after('alternate_mobile');
            $table->string('alternate_email')->nullable()->after('email');
            $table->string('contact_person_name', 100)->nullable()->after('address');
            $table->string('contact_person_phone', 20)->nullable()->after('contact_person_name');
            $table->string('pan_number', 20)->nullable()->after('gstin');
            $table->string('aadhar_number', 20)->nullable()->after('pan_number');
            $table->date('registration_date')->nullable()->after('aadhar_number');
            $table->enum('dealer_type', ['retailer', 'wholesaler', 'distributor', 'other'])->nullable()->after('registration_date');
            $table->decimal('credit_limit', 12, 2)->default(0)->after('dealer_type');
            $table->integer('credit_days')->default(0)->after('credit_limit');
            $table->text('bank_details')->nullable()->after('credit_days');
            $table->text('notes')->nullable()->after('bank_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'alternate_mobile',
                'phone',
                'alternate_email',
                'contact_person_name',
                'contact_person_phone',
                'pan_number',
                'aadhar_number',
                'registration_date',
                'dealer_type',
                'credit_limit',
                'credit_days',
                'bank_details',
                'notes',
            ]);
        });
    }
};
