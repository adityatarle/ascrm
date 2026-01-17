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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('cheque_number')->nullable()->after('transaction_id');
            $table->string('bank_name')->nullable()->after('cheque_number');
            $table->string('reference_number')->nullable()->after('bank_name');
            $table->text('notes')->nullable()->after('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['cheque_number', 'bank_name', 'reference_number', 'notes']);
        });
    }
};
