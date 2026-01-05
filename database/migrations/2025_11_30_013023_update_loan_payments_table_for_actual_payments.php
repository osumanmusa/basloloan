<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            // Add reference to payment schedule
            $table->foreignId('loan_payment_schedule_id')->nullable()->constrained()->onDelete('cascade');
            
            // Remove due_date since it will come from the schedule
            $table->dropColumn('due_date');
            
            // Add payment type to distinguish between scheduled and ad-hoc payments
            $table->enum('payment_type', ['scheduled', 'adhoc', 'early_settlement'])->default('scheduled');
            
            // Make payment_date not nullable since it's an actual payment
            $table->date('payment_date')->nullable(false)->change();
            
            // Make received_by not nullable since it's an actual payment
            $table->foreignId('received_by')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            $table->dropForeign(['loan_payment_schedule_id']);
            $table->dropColumn('loan_payment_schedule_id');
            $table->dropColumn('payment_type');
            $table->date('due_date');
            $table->date('payment_date')->nullable()->change();
            $table->foreignId('received_by')->nullable()->change();
        });
    }
};