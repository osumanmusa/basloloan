<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->decimal('amount', 12, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term_months');
            $table->enum('purpose', ['personal', 'business', 'education', 'medical', 'home', 'car', 'other']);
            $table->text('purpose_description')->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'disbursed', 'active', 'completed', 'defaulted'])->default('pending');
            $table->enum('type', ['personal', 'business', 'mortgage', 'auto', 'education']);
            $table->decimal('monthly_payment', 10, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->decimal('remaining_balance', 12, 2)->nullable();
            $table->date('disbursement_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};