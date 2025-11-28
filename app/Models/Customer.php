<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state',
        'zip_code', 'date_of_birth', 'id_number', 'monthly_income', 'employment_status',
        'employment_details', 'credit_score'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'monthly_income' => 'decimal:2',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Add this method to calculate age
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }
}