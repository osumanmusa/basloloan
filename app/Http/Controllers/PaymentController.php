<?php

namespace App\Http\Controllers;

use App\Models\LoanPayment;
use App\Models\Loan;
use App\Models\Customer;
use App\Models\LoanPaymentSchedule; 
use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Add this import

class PaymentController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:view payments')->only('index', 'show');
        // $this->middleware('permission:create payments')->only(['create', 'store']);
        // $this->middleware('permission:edit payments')->only(['edit', 'update']);
        // $this->middleware('permission:delete payments')->only('destroy');
        // $this->middleware('permission:verify payments')->only('complete');
    }

  public function index(Request $request)
{
    // Check permission manually
    if (!auth()->user()->can('view-payments')) {
        abort(403);
    }

    $payments = LoanPayment::with(['loan.customer', 'receiver', 'schedule'])
        ->when($request->search, function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('loan.customer', function($customerQuery) use ($search) {
                    $customerQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('reference_number', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
            });
        })
        ->when($request->status, function($query, $status) {
            $query->where('status', $status);
        })
        ->when($request->payment_method, function($query, $method) {
            $query->where('payment_method', $method);
        })
        ->when($request->payment_type, function($query, $type) {
            $query->where('payment_type', $type);
        })
        ->when($request->date_from, function($query, $dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        })
        ->when($request->date_to, function($query, $dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        })
        ->latest()
        ->paginate(15)
        ->withQueryString();

    // Get stats for the dashboard
    $stats = [
        'total_payments' => LoanPayment::count(),
        'completed_payments' => LoanPayment::where('status', 'completed')->count(),
        'pending_payments' => LoanPayment::where('status', 'pending')->count(),
        'overdue_payments' => LoanPaymentSchedule::where('status', 'pending')
            ->whereDate('due_date', '<', now())
            ->count(),
    ];

    // Get available loans for filter
    $availableLoans = Loan::whereIn('status', ['active', 'disbursed'])
        ->with('customer')
        ->get(['id', 'customer_id'])
        ->map(function($loan) {
            return [
                'id' => $loan->id,
                'customer' => $loan->customer
            ];
        });

    return Inertia::render('Payments/index', [
        'payments' => $payments,
        'filters' => $request->only(['search', 'status', 'payment_method', 'payment_type', 'date_from', 'date_to']),
        'stats' => $stats,
        'availableLoans' => $availableLoans,
    ]);
}

    // public function create()
    // {
    //     // Get active loans with their customers and remaining balances
    //     $loans = Loan::whereIn('status', ['active', 'disbursed'])
    //         ->with('customer')
    //         ->where('remaining_balance', '>', 0)
    //         ->get()
    //         ->map(function ($loan) {
    //             return [
    //                 'id' => $loan->id,
    //                 'customer' => $loan->customer,
    //                 'remaining_balance' => $loan->remaining_balance,
    //                 'monthly_payment' => $loan->monthly_payment,
    //                 'status' => $loan->status,
    //             ];
    //         });

    //     return Inertia::render('Payments/Create', [
    //         'loans' => $loans,
    //     ]);
    // }
public function create()
{
    // Check permission manually
    if (!auth()->user()->can('create-payments')) {
        abort(403);
    }

    // Get active loans with their payment schedules (only pending/overdue)
    $loans = Loan::whereIn('status', ['active', 'disbursed'])
        ->with(['customer', 'paymentSchedules' => function($query) {
            $query->whereIn('status', ['pending', 'overdue'])->orderBy('due_date');
        }])
        ->where('remaining_balance', '>', 0)
        ->get()
        ->map(function ($loan) {
            return [
                'id' => $loan->id,
                'customer' => $loan->customer,
                'remaining_balance' => $loan->remaining_balance,
                'monthly_payment' => $loan->monthly_payment,
                'status' => $loan->status,
                'payment_schedules' => $loan->paymentSchedules->map(function($schedule) {
                    return [
                        'id' => $schedule->id,
                        'installment_number' => $schedule->installment_number,
                        'due_amount' => $schedule->due_amount,
                        'principal_amount' => $schedule->principal_amount,
                        'interest_amount' => $schedule->interest_amount,
                        'due_date' => $schedule->due_date,
                        'status' => $schedule->status,
                        'paid_amount' => $schedule->paid_amount,
                        'remaining_amount' => $schedule->remaining_amount,
                    ];
                })->toArray()
            ];
        })->filter(function($loan) {
            // Only include loans that have pending/overdue schedules
            return count($loan['payment_schedules']) > 0;
        });

    return Inertia::render('Payments/Create', [
        'loans' => $loans,
    ]);
}
public function store(Request $request)
{
    // Check permission manually
    if (!auth()->user()->can('create-payments')) {
        abort(403);
    }

    $request->validate([
        'loan_id' => 'required|exists:loans,id',
        'loan_payment_schedule_id' => 'nullable|exists:loan_payment_schedules,id',
        'amount' => 'required|numeric|min:0.01',
        'principal_amount' => 'nullable|numeric|min:0',
        'interest_amount' => 'nullable|numeric|min:0',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,bank_transfer,check,mobile_money,online',
        'payment_type' => 'required|in:scheduled,adhoc,early_settlement',
        'reference_number' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:500',
        'status' => 'required|in:pending,completed,late,partial',
    ]);

    $loan = Loan::with('customer')->findOrFail($request->loan_id);
    
    try {
        DB::beginTransaction();

        // Create payment
        $payment = LoanPayment::create([
            'loan_id' => $request->loan_id,
            'loan_payment_schedule_id' => $request->loan_payment_schedule_id,
            'received_by' => auth()->id(),
            'amount' => $request->amount,
            'principal_amount' => $request->principal_amount ?? 0,
            'interest_amount' => $request->interest_amount ?? 0,
            'payment_date' => $request->payment_date,
            'status' => $request->status,
            'payment_method' => $request->payment_method,
            'payment_type' => $request->payment_type,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
        ]);

        // Update loan balance if payment is completed
        if ($request->status === 'completed' && $request->principal_amount > 0) {
            $loan->decrement('remaining_balance', $request->principal_amount);
            
            // Check if loan is fully paid
            if ($loan->remaining_balance <= 0) {
                $loan->update(['status' => 'completed']);
                
                // Update all pending payment schedules
                LoanPaymentSchedule::where('loan_id', $loan->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            }
        }

        // Update payment schedule if this is a scheduled payment
        if ($request->loan_payment_schedule_id && $request->status === 'completed') {
            $schedule = LoanPaymentSchedule::find($request->loan_payment_schedule_id);
            if ($schedule) {
                $paidAmount = $schedule->payments()->where('status', 'completed')->sum('amount');
                if ($paidAmount >= $schedule->due_amount) {
                    $schedule->update(['status' => 'paid']);
                    
                    // Return success response with redirect
                    DB::commit();
                    return redirect()->route('payments.index')
                        ->with('success', 'Payment recorded successfully! Scheduled payment marked as paid.');
                }
            }
        }

        DB::commit();

        // Return success response with redirect
        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Return error response with redirect back
        return redirect()->back()
            ->with('error', 'Error recording payment: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show(LoanPayment $payment)
    {
        $payment->load(['loan.customer', 'receiver']);

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function edit(LoanPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending payments can be edited.');
        }

        $payment->load(['loan.customer']);

        $loans = Loan::whereIn('status', ['active', 'disbursed'])
            ->with('customer')
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'display' => "Loan #{$loan->id} - {$loan->customer->first_name} {$loan->customer->last_name} - $" . number_format($loan->amount),
                ];
            });

        return Inertia::render('Payments/Edit', [
            'payment' => $payment,
            'loans' => $loans,
        ]);
    }

    public function update(Request $request, LoanPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending payments can be edited.');
        }

        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:0.01',
            'principal_amount' => 'nullable|numeric|min:0',
            'interest_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'due_date' => 'nullable|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,mobile_money,online',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->update($request->all());

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment updated successfully!');
    }

    public function destroy(LoanPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending payments can be deleted.');
        }

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully!');
    }

    public function complete(LoanPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending payments can be marked as completed.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'completed',
                'payment_date' => now(),
                'received_by' => auth()->id(),
            ]);

            // Update loan remaining balance
            if ($payment->principal_amount && $payment->principal_amount > 0) {
                $payment->loan->decrement('remaining_balance', $payment->principal_amount);
                
                // If loan is fully paid, update status
                if ($payment->loan->remaining_balance <= 0) {
                    $payment->loan->update(['status' => 'completed']);
                }
            }
        });

        return redirect()->back()->with('success', 'Payment marked as completed!');
    }




    /**
     * Process payment against a scheduled payment (partial or full)
     */
    public function processScheduledPayment(Request $request, LoanPayment $scheduledPayment)
    {
        if (!auth()->user()->can('create payments')) {
            abort(403);
        }

        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,mobile_money,online',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $loan = $scheduledPayment->loan;

        try {
            DB::beginTransaction();

            $amountPaid = $request->amount_paid;
            $scheduledAmount = $scheduledPayment->amount;

            if ($amountPaid >= $scheduledAmount) {
                // Full payment
                $scheduledPayment->update([
                    'status' => 'completed',
                    'payment_date' => $request->payment_date,
                    'received_by' => auth()->id(),
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'notes' => $request->notes,
                ]);

                // Update loan balance
                if ($scheduledPayment->principal_amount > 0) {
                    $loan->decrement('remaining_balance', $scheduledPayment->principal_amount);
                }
            } else {
                // Partial payment
                $principalRatio = $scheduledPayment->principal_amount / $scheduledAmount;
                $interestRatio = $scheduledPayment->interest_amount / $scheduledAmount;

                $principalPaid = $amountPaid * $principalRatio;
                $interestPaid = $amountPaid * $interestRatio;

                // Create a new partial payment record
                $partialPayment = LoanPayment::create([
                    'loan_id' => $scheduledPayment->loan_id,
                    'received_by' => auth()->id(),
                    'amount' => $amountPaid,
                    'principal_amount' => $principalPaid,
                    'interest_amount' => $interestPaid,
                    'payment_date' => $request->payment_date,
                    'due_date' => $scheduledPayment->due_date,
                    'status' => 'partial',
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'notes' => $request->notes . ' (Partial payment for scheduled payment #' . $scheduledPayment->id . ')',
                ]);

                // Update scheduled payment amount
                $remainingAmount = $scheduledAmount - $amountPaid;
                $scheduledPayment->update([
                    'amount' => $remainingAmount,
                    'principal_amount' => $scheduledPayment->principal_amount - $principalPaid,
                    'interest_amount' => $scheduledPayment->interest_amount - $interestPaid,
                ]);

                // Update loan balance
                $loan->decrement('remaining_balance', $principalPaid);
            }

            // Check if loan is fully paid
            if ($loan->remaining_balance <= 0) {
                $loan->update(['status' => 'completed']);
                
                // Cancel remaining pending payments
                LoanPayment::where('loan_id', $loan->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            }

            DB::commit();

            return redirect()->route('payments.show', $partialPayment->id ?? $scheduledPayment->id)
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Get payment receipt data
     */
    public function receipt(LoanPayment $payment)
    {
        $payment->load(['loan.customer', 'receiver']);

        return Inertia::render('Payments/Receipt', [
            'payment' => $payment,
        ]);
    }

    /**
     * Export payment as PDF
     */
    public function exportPdf(LoanPayment $payment)
    {
        $payment->load(['loan.customer', 'receiver']);

        // You'll need to install a PDF package like barryvdh/laravel-dompdf
        // composer require barryvdh/laravel-dompdf

        $pdf = \PDF::loadView('pdf.payment-receipt', [
            'payment' => $payment,
            'date' => now()->format('F j, Y'),
        ]);

        return $pdf->download("payment-receipt-{$payment->id}.pdf");
    }

    /**
     * Export loan payment history as Excel
     */
    public function exportLoanPayments(Loan $loan)
    {
        // You'll need to install a Excel package like maatwebsite/excel
        // composer require maatwebsite/excel

        $payments = $loan->payments()->with('receiver')->get();

        return \Excel::download(new \App\Exports\LoanPaymentsExport($payments), "loan-{$loan->id}-payments.xlsx");
    }

    /**
     * Get all payments for a specific loan
     */
    public function loanPayments(Loan $loan)
    {
        $payments = $loan->payments()
            ->with('receiver')
            ->orderBy('payment_date', 'desc')
            ->get();

        return Inertia::render('Payments/LoanPayments', [
            'loan' => $loan->load('customer'),
            'payments' => $payments,
        ]);
    }





}