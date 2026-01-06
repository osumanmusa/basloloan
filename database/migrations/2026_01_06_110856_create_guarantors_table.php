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
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('address');
            $table->enum('relationship', [
                'parent',
                'spouse', 
                'sibling',
                'friend',
                'colleague',
                'relative',
                'other'
            ]);
            $table->enum('id_type', [
                'ghana_card',
                'voters_id',
                'drivers_license',
                'passport',
                'national_id'
            ]);
            $table->string('id_number');
            $table->string('id_front_path')->nullable();
            $table->string('id_back_path')->nullable();
            $table->string('passport_picture_path')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['loan_id']);
            $table->index(['id_type']);
            $table->index(['relationship']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantors');
    }
};