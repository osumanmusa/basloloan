<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->integer('installment_number'); // 1, 2, 3, etc.
            $table->decimal('due_amount', 10, 2);
            $table->decimal('principal_amount', 10, 2);
            $table->decimal('interest_amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['loan_id', 'installment_number']);
            $table->index(['due_date', 'status']);            
            $table->index(['loan_id', 'due_date']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payment_schedules');
    }
};