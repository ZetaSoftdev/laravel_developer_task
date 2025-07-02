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
            // Add transaction_id field if it doesn't exist
            if (!Schema::hasColumn('payment_transactions', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_id');
            }
            
            // Add payment_receipt field if it doesn't exist  
            if (!Schema::hasColumn('payment_transactions', 'payment_receipt')) {
                $table->string('payment_receipt')->nullable()->after('transaction_id');
            }
            
            // Add status field if it doesn't exist
            if (!Schema::hasColumn('payment_transactions', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('payment_receipt');
            }
            
            // Add metadata field if it doesn't exist
            if (!Schema::hasColumn('payment_transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'payment_receipt', 'status', 'metadata']);
        });
    }
};
