<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'created_by', 'approved_by', 'amount', 'interest_rate', 'term_months',
        'purpose', 'purpose_description', 'status', 'type', 'monthly_payment', 'total_amount',
        'remaining_balance', 'disbursement_date', 'due_date', 'approved_at', 'rejection_reason', 'admin_notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'disbursement_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    /**
     * Calculate loan details including monthly payment, total amount, etc.
     */
    public function calculateLoanDetails(): void
    {
        $principal = (float) $this->amount;
        $annualRate = (float) $this->interest_rate;
        $termMonths = (int) $this->term_months;

        $monthlyRate = $annualRate / 100 / 12;
        
        if ($monthlyRate > 0) {
            $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        } else {
            $monthlyPayment = $principal / $termMonths;
        }

        $totalPayment = $monthlyPayment * $termMonths;
        $totalInterest = $totalPayment - $principal;

        // Update the loan with calculated values
        $this->update([
            'monthly_payment' => round($monthlyPayment, 2),
            'total_amount' => round($totalPayment, 2),
            'remaining_balance' => round($principal, 2), // Initially, remaining balance equals principal
        ]);
    }

    /**
     * Check if loan can be disbursed
     */
    public function canBeDisbursed(): bool
    {
        return $this->status === 'approved' && $this->remaining_balance === null;
    }

    /**
     * Get the next due date for payment
     */
    public function getNextDueDate(): ?string
    {
        if (!$this->disbursement_date) {
            return null;
        }

        $lastPayment = $this->payments()->latest('due_date')->first();
        
        if ($lastPayment) {
            return \Carbon\Carbon::parse($lastPayment->due_date)->addMonth()->format('Y-m-d');
        }

        return \Carbon\Carbon::parse($this->disbursement_date)->addMonth()->format('Y-m-d');
    }
    // In Loan.php model
public function paymentSchedules(): HasMany
{
    return $this->hasMany(LoanPaymentSchedule::class);
}

public function generatePaymentSchedule(): void
{
    $principal = (float) $this->amount;
    $annualRate = (float) $this->interest_rate;
    $termMonths = (int) $this->term_months;

    $monthlyRate = $annualRate / 100 / 12;
    
    if ($monthlyRate > 0) {
        $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
    } else {
        $monthlyPayment = $principal / $termMonths;
    }

    $remainingBalance = $principal;
    $paymentDate = now();

    for ($i = 1; $i <= $termMonths; $i++) {
        $interestAmount = $remainingBalance * $monthlyRate;
        $principalAmount = $monthlyPayment - $interestAmount;
        
        // For the last payment, adjust to ensure we don't overpay
        if ($i === $termMonths) {
            $principalAmount = $remainingBalance;
            $monthlyPayment = $principalAmount + $interestAmount;
        }

        LoanPaymentSchedule::create([
            'loan_id' => $this->id,
            'installment_number' => $i,
            'due_amount' => round($monthlyPayment, 2),
            'principal_amount' => round($principalAmount, 2),
            'interest_amount' => round($interestAmount, 2),
            'due_date' => $paymentDate->copy()->addMonths($i),
            'status' => 'pending',
            'notes' => "Installment #{$i} for Loan #{$this->id}",
        ]);

        $remainingBalance -= $principalAmount;
    }
}
}