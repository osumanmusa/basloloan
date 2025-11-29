<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('received_by')->nullable()->constrained('users'); // Make nullable
            $table->decimal('amount', 10, 2);
            $table->decimal('principal_amount', 10, 2);
            $table->decimal('interest_amount', 10, 2);
            $table->date('payment_date')->nullable(); // Make nullable for scheduled payments
            $table->date('due_date');
            $table->enum('status', ['pending', 'completed', 'late', 'partial'])->default('pending');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'online', 'mobile_money']);
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};