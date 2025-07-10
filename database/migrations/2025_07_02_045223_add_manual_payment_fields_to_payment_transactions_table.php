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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('payment_status');
            $table->string('receipt')->nullable()->after('transaction_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('receipt');
            $table->json('metadata')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'receipt', 'status', 'metadata']);
        });
    }
};
