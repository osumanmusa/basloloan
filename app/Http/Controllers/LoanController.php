<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Customer;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\LoanPayment;
use Carbon\Carbon;
use DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::with('customer')
            ->when($request->search, function($query, $search) {
                $query->whereHas('customer', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Loans/index', [
            'loans' => $loans,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        // $customers = Customer::all();
        // return Inertia::render('Loans/Create', [
        //     'customers' => $customers,
        // ]);


    $customers = Customer::select('id', 'first_name', 'last_name', 'email')
        ->orderBy('first_name')
        ->get()
        ->map(function ($customer) {
            return [
                'id' => $customer->id,
                'full_name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => $customer->email,
            ];
        });

    return Inertia::render('Loans/Create', [
        'customers' => $customers,
    ]);

    }
public function store(Request $request)
{
    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric|min:100',
        'interest_rate' => 'required|numeric|min:0.1|max:50',
        'term_months' => 'required|integer|min:1|max:360',
        'purpose' => 'required|string',
        'type' => 'required|string',
        'purpose_description' => 'nullable|string|max:1000',
    ]);

    $loan = Loan::create([
        'customer_id' => $request->customer_id,
        'created_by' => auth()->id(),
        'amount' => $request->amount,
        'interest_rate' => $request->interest_rate,
        'term_months' => $request->term_months,
        'purpose' => $request->purpose,
        'type' => $request->type,
        'purpose_description' => $request->purpose_description,
        'status' => 'pending', // Always start as pending
    ]);

    return redirect()->route('loans.index')->with('success', 'Loan created successfully!');
}

    public function show(Loan $loan)
    {
        $loan->load(['customer', 'creator', 'approver', 'payments']);
        return Inertia::render('Loans/Show', [
            'loan' => $loan,
        ]);
    }
public function edit(Loan $loan)
{
    // Check if user owns the loan or is admin
    if ($loan->created_by !== auth()->id() && !auth()->user()->hasRole('admin')) {
        abort(403);
    }

    // Only pending loans can be edited
    if ($loan->status !== 'pending') {
        return redirect()->route('loans.show', $loan->id)->with('error', 'Only pending loans can be edited. This loan is already ' . $loan->status . '.');
    }

    $customers = Customer::select('id', 'first_name', 'last_name', 'email')
        ->orderBy('first_name')
        ->get()
        ->map(function ($customer) {
            return [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
            ];
        });

    return Inertia::render('Loans/Edit', [
        'loan' => $loan->load('customer'),
        'customers' => $customers,
    ]);
}

public function update(Request $request, Loan $loan)
{
    // Check if user owns the loan or is admin
    if ($loan->created_by !== auth()->id() && !auth()->user()->hasRole('admin')) {
        abort(403);
    }

    // Only pending loans can be edited
    if ($loan->status !== 'pending') {
        return redirect()->route('loans.show', $loan->id)->with('error', 'Only pending loans can be edited. This loan is already ' . $loan->status . '.');
    }

    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric|min:100',
        'interest_rate' => 'required|numeric|min:0.1|max:50',
        'term_months' => 'required|integer|min:1|max:360',
        'purpose' => 'required|string',
        'type' => 'required|string',
        'purpose_description' => 'nullable|string|max:1000',
    ]);

    $loan->update($request->all());

    return redirect()->route('loans.show', $loan->id)->with('success', 'Loan updated successfully!');
}

public function destroy(Loan $loan)
{
    // Check permission
    if (!auth()->user()->can('delete loans')) {
        abort(403);
    }

    // Only pending loans can be deleted
    if ($loan->status !== 'pending') {
        return redirect()->back()->with('error', 'Only pending loans can be deleted. This loan is already ' . $loan->status . '.');
    }

    // Check if user owns the loan or is admin
    if ($loan->created_by !== auth()->id() && !auth()->user()->hasRole('admin')) {
        abort(403);
    }

    $loan->delete();

    return redirect()->route('loans.index')->with('success', 'Loan deleted successfully!');
}
public function approve(Loan $loan)
{
    if (!auth()->user()->can('approve-loans')) {
        abort(403);
    }

    // Only pending loans can be approved
    if ($loan->status !== 'pending') {
        return redirect()->back()->with('error', 'Only pending loans can be approved. This loan is already ' . $loan->status . '.');
    }

    $loan->update([
        'status' => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Loan approved successfully!');
}

public function reject(Loan $loan)
{
    if (!auth()->user()->can('approve loans')) {
        abort(403);
    }

    // Only pending loans can be rejected
    if ($loan->status !== 'pending') {
        return redirect()->back()->with('error', 'Only pending loans can be rejected. This loan is already ' . $loan->status . '.');
    }

    $loan->update([
        'status' => 'rejected',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Loan rejected successfully!');
}
 public function disburse(Loan $loan)
{
    if (!auth()->user()->can('disburse-loans')) {
        abort(403);
    }

    if ($loan->status !== 'approved') {
        return redirect()->back()->with('error', 'Only approved loans can be disbursed.');
    }

    try {
        DB::beginTransaction();

        // Calculate loan details using the model method
        $loan->calculateLoanDetails();
        
        // Update loan status and dates
        $loan->update([
            'status' => 'disbursed',
            'disbursement_date' => now(),
            'due_date' => now()->addMonths($loan->term_months),
        ]);

        // Create payment schedule
        $this->createPaymentSchedule($loan);

        DB::commit();

        return redirect()->back()->with('success', 'Loan disbursed successfully! Payment schedule created.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error disbursing loan: ' . $e->getMessage());
    }
}

    public function calculate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'interest_rate' => 'required|numeric|min:0.1|max:50',
            'term_months' => 'required|integer|min:1|max:360',
        ]);

        $results = $this->calculateLoanDetails(
            $request->amount,
            $request->interest_rate,
            $request->term_months
        );

        return response()->json(['results' => $results]);
    }

    private function calculateLoanDetails($principal, $annualRate, $termMonths)
    {
        $monthlyRate = $annualRate / 100 / 12;
        
        if ($monthlyRate > 0) {
            $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        } else {
            $monthlyPayment = $principal / $termMonths;
        }

        $totalPayment = $monthlyPayment * $termMonths;
        $totalInterest = $totalPayment - $principal;

        // Generate amortization schedule
        $balance = $principal;
        $amortizationSchedule = [];

        for ($month = 1; $month <= $termMonths; $month++) {
            $interest = $balance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interest;
            $balance -= $principalPayment;

            $amortizationSchedule[] = [
                'month' => $month,
                'payment' => number_format($monthlyPayment, 2),
                'principal' => number_format($principalPayment, 2),
                'interest' => number_format($interest, 2),
                'balance' => number_format(max($balance, 0), 2),
            ];
        }

        return [
            'monthly_payment' => number_format($monthlyPayment, 2),
            'total_payment' => number_format($totalPayment, 2),
            'total_interest' => number_format($totalInterest, 2),
            'amortization_schedule' => $amortizationSchedule,
        ];
    }
private function createPaymentSchedule(Loan $loan)
{
    $paymentDate = now();
    
    for ($i = 1; $i <= $loan->term_months; $i++) {
        LoanPayment::create([
            'loan_id' => $loan->id,
            'amount' => $loan->monthly_payment,
            'principal_amount' => 0, // Will be calculated when payment is made
            'interest_amount' => 0,  // Will be calculated when payment is made
            'due_date' => $paymentDate->copy()->addMonths($i),
            'payment_date' => null, // NULL for scheduled payments (now allowed)
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'received_by' => null, // NULL for scheduled payments (now allowed)
            'reference_number' => 'SCH-' . $loan->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
            'notes' => 'Scheduled payment #' . $i . ' for Loan #' . $loan->id,
        ]);
    }
}
}


