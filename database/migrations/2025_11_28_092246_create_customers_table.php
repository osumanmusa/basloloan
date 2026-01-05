<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->date('date_of_birth');
 
    $table->string('id_type')->nullable();
    $table->string('id_number')->nullable();
    $table->string('passport_picture')->nullable();
    $table->string('id_front')->nullable();
    $table->string('id_back')->nullable();

            $table->decimal('monthly_income', 10, 2);
            $table->enum('employment_status', ['employed', 'self-employed', 'unemployed', 'retired']);
            $table->text('employment_details')->nullable();
            $table->integer('credit_score')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};