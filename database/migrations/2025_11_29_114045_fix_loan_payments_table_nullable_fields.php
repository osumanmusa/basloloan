<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            // First drop the existing foreign key constraint if it exists
            $table->dropForeign(['received_by']);
            
            // Make payment_date nullable (for scheduled payments that haven't been paid yet)
            $table->date('payment_date')->nullable()->change();
            
            // Make received_by nullable (for scheduled payments) and re-add foreign key
            $table->unsignedBigInteger('received_by')->nullable()->change();
            $table->foreign('received_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('loan_payments', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['received_by']);
            
            // Make columns not nullable again
            $table->date('payment_date')->nullable(false)->change();
            $table->unsignedBigInteger('received_by')->nullable(false)->change();
            
            // Re-add the original foreign key constraint
            $table->foreign('received_by')->references('id')->on('users');
        });
    }
};