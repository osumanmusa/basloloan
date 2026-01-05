<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'loan_payment_schedule_id',
        'received_by',
        'amount',
        'principal_amount',
        'interest_amount',
        'payment_date',
        'status',
        'payment_method',
        'payment_type',
        'reference_number',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(LoanPaymentSchedule::class, 'loan_payment_schedule_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function isPartialPayment(): bool
    {
        if (!$this->schedule) return false;
        return $this->amount < $this->schedule->due_amount;
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
        
        // Update the schedule status if this payment covers the full amount
        if ($this->schedule && $this->amount >= $this->schedule->due_amount) {
            $this->schedule->markAsPaid();
        }
    }
}