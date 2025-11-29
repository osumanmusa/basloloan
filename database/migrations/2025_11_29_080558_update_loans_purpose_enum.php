<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('purpose', [
                'debt_consolidation',
                'home_improvement', 
                'business_expansion',
                'education',
                'medical',
                'vehicle',
                'wedding',
                'vacation',
                'other'
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('purpose', [
                'personal',
                'business', 
                'education',
                'medical',
                'home',
                'car',
                'other'
            ])->change();
        });
    }
};