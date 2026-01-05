<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanPaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_amount',
        'principal_amount',
        'interest_amount',
        'due_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'due_amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class, 'loan_payment_schedule_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    public function isDueSoon(): bool
    {
        return $this->status === 'pending' && $this->due_date->diffInDays(now()) <= 7;
    }

    // public function getPaidAmountAttribute(): float
    // {
    //     return $this->payments()->where('status', 'completed')->sum('amount');
    // }

    // public function getRemainingAmountAttribute(): float
    // {
    //     return $this->due_amount - $this->paid_amount;
    // }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }
}