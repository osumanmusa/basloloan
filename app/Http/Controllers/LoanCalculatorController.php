<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class LoanCalculatorController extends Controller
{
    public function index()
    {
        return Inertia::render('Calculator/index');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'loan_amount' => 'required|numeric|min:100',
            'interest_rate' => 'required|numeric|min:0.1|max:50',
            'loan_term' => 'required|integer|min:1|max:360',
            'term_type' => 'required|in:months,years',
        ]);

        $principal = $request->loan_amount;
        $annualRate = $request->interest_rate;
        $loanTerm = $request->loan_term;
        $termType = $request->term_type;

        // Convert years to months if needed
        if ($termType === 'years') {
            $loanTerm = $loanTerm * 12;
        }

        $results = $this->calculateLoanDetails($principal, $annualRate, $loanTerm);

        // Return Inertia response instead of JSON
        return Inertia::render('Calculator/index', [
            'calculationResults' => $results,
            'formData' => $request->all() // Pass back the form data to repopulate fields
        ]);
    }

    private function calculateLoanDetails($principal, $annualRate, $termMonths)
    {
        $monthlyRate = $annualRate / 100 / 12;
        
        // Calculate monthly payment
        if ($monthlyRate > 0) {
            $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        } else {
            $monthlyPayment = $principal / $termMonths;
        }

        $totalPayment = $monthlyPayment * $termMonths;
        $totalInterest = $totalPayment - $principal;

        // Generate amortization schedule (first 12 months for preview)
        $balance = $principal;
        $amortizationSchedule = [];

        $monthsToShow = min($termMonths, 12); // Show only first 12 months in preview

        for ($month = 1; $month <= $monthsToShow; $month++) {
            $interest = $balance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interest;
            
            // For the last payment in preview, don't adjust
            if ($month === $termMonths) {
                $principalPayment = $balance;
            }

            $balance -= $principalPayment;

            $amortizationSchedule[] = [
                'month' => $month,
                'payment_date' => now()->addMonths($month)->format('M Y'),
                'payment' => round($monthlyPayment, 2),
                'principal' => round($principalPayment, 2),
                'interest' => round($interest, 2),
                'balance' => round(max($balance, 0), 2),
            ];
        }

        return [
            'monthly_payment' => round($monthlyPayment, 2),
            'total_payment' => round($totalPayment, 2),
            'total_interest' => round($totalInterest, 2),
            'term_months' => $termMonths,
            'principal' => round($principal, 2),
            'interest_rate' => round($annualRate, 2),
            'amortization_schedule' => $amortizationSchedule,
            'has_more_months' => $termMonths > 12,
            'total_months' => $termMonths,
        ];
    }
}