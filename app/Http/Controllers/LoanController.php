<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Customer;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\LoanPayment;
use Carbon\Carbon;
use DB;
use App\Models\Guarantor;
use Illuminate\Support\Facades\Storage;

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
// public function store(Request $request)
// {
//     $request->validate([
//         'customer_id' => 'required|exists:customers,id',
//         'amount' => 'required|numeric|min:100',
//         'interest_rate' => 'required|numeric|min:0.1|max:50',
//         'term_months' => 'required|integer|min:1|max:360',
//         'purpose' => 'required|string',
//         'type' => 'required|string',
//         'purpose_description' => 'nullable|string|max:1000',
//     ]);

//     $loan = Loan::create([
//         'customer_id' => $request->customer_id,
//         'created_by' => auth()->id(),
//         'amount' => $request->amount,
//         'interest_rate' => $request->interest_rate,
//         'term_months' => $request->term_months,
//         'purpose' => $request->purpose,
//         'type' => $request->type,
//         'purpose_description' => $request->purpose_description,
//         'status' => 'pending', // Always start as pending
//     ]);

//     return redirect()->route('loans.index')->with('success', 'Loan created successfully!');
// }
public function store(Request $request)
{
    // Validate loan data
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric|min:100',
        'interest_rate' => 'required|numeric|min:0.1|max:50',
        'term_months' => 'required|integer|min:1|max:360',
        'purpose' => 'required|string',
        'type' => 'required|string',
        'purpose_description' => 'nullable|string|max:1000',
        'guarantor.full_name' => 'required|string|max:255',
        'guarantor.email' => 'nullable|email|max:255',
        'guarantor.phone' => 'required|string|max:20',
        'guarantor.address' => 'required|string|max:1000',
        'guarantor.relationship' => 'required|string|max:100',
        'guarantor.id_type' => 'required|string|max:50',
        'guarantor.id_number' => 'required|string|max:100',
        'guarantor.id_front' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        'guarantor.id_back' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        'guarantor.passport_picture' => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5MB
    ]);

    try {
        DB::beginTransaction();

        // Create the loan
        $loan = Loan::create([
            'customer_id' => $request->customer_id,
            'created_by' => auth()->id(),
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate,
            'term_months' => $request->term_months,
            'purpose' => $request->purpose,
            'type' => $request->type,
            'purpose_description' => $request->purpose_description,
            'status' => 'pending',
        ]);

        // Handle file uploads
        $idFrontPath = null;
        $idBackPath = null;
        $passportPicturePath = null;

        if ($request->hasFile('guarantor.id_front')) {
            $idFrontPath = $request->file('guarantor.id_front')->store('guarantor-documents', 'public');
        }

        if ($request->hasFile('guarantor.id_back')) {
            $idBackPath = $request->file('guarantor.id_back')->store('guarantor-documents', 'public');
        }

        if ($request->hasFile('guarantor.passport_picture')) {
            $passportPicturePath = $request->file('guarantor.passport_picture')->store('guarantor-passports', 'public');
        }

        // Create guarantor record
        $guarantorData = [
            'loan_id' => $loan->id,
            'full_name' => $request->input('guarantor.full_name'),
            'email' => $request->input('guarantor.email'),
            'phone' => $request->input('guarantor.phone'),
            'address' => $request->input('guarantor.address'),
            'relationship' => $request->input('guarantor.relationship'),
            'id_type' => $request->input('guarantor.id_type'),
            'id_number' => $request->input('guarantor.id_number'),
            'id_front_path' => $idFrontPath,
            'id_back_path' => $idBackPath,
            'passport_picture_path' => $passportPicturePath,
        ];

        // Remove null values from array (for optional fields)
        $guarantorData = array_filter($guarantorData, function($value) {
            return $value !== null;
        });

        Guarantor::create($guarantorData);

        DB::commit();

        return redirect()->route('loans.index')->with('success', 'Loan created successfully with guarantor information!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Log the error for debugging
        \Log::error('Loan creation failed: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create loan. Please try again.']);
    }
}
public function show(Loan $loan)
{
    $loan->load(['customer', 'creator', 'approver', 'payments', 'guarantor']);
    
    // Add URL accessors to guarantor if it exists
    if ($loan->guarantor) {
        $loan->guarantor->id_front_url = $loan->guarantor->id_front_path ? asset('storage/' . $loan->guarantor->id_front_path) : null;
        $loan->guarantor->id_back_url = $loan->guarantor->id_back_path ? asset('storage/' . $loan->guarantor->id_back_path) : null;
        $loan->guarantor->passport_picture_url = $loan->guarantor->passport_picture_path ? asset('storage/' . $loan->guarantor->passport_picture_path) : null;
    }
    
    return Inertia::render('Loans/Show', [
        'loan' => $loan,
    ]);
}
// public function edit(Loan $loan)
// {
//     // Check if user owns the loan or is admin
//     if ($loan->created_by !== auth()->id() && !auth()->user()->hasRole('admin')) {
//         abort(403);
//     }

//     // Only pending loans can be edited
//     if ($loan->status !== 'pending') {
//         return redirect()->route('loans.show', $loan->id)->with('error', 'Only pending loans can be edited. This loan is already ' . $loan->status . '.');
//     }

//     $customers = Customer::select('id', 'first_name', 'last_name', 'email')
//         ->orderBy('first_name')
//         ->get()
//         ->map(function ($customer) {
//             return [
//                 'id' => $customer->id,
//                 'first_name' => $customer->first_name,
//                 'last_name' => $customer->last_name,
//                 'email' => $customer->email,
//             ];
//         });

//     return Inertia::render('Loans/Edit', [
//         'loan' => $loan->load('customer'),
//         'customers' => $customers,
//     ]);
// }

// public function update(Request $request, Loan $loan)
// {
//     // Check if user owns the loan or is admin
//     if ($loan->created_by !== auth()->id() && !auth()->user()->hasRole('admin')) {
//         abort(403);
//     }

//     // Only pending loans can be edited
//     if ($loan->status !== 'pending') {
//         return redirect()->route('loans.show', $loan->id)->with('error', 'Only pending loans can be edited. This loan is already ' . $loan->status . '.');
//     }

//     $request->validate([
//         'customer_id' => 'required|exists:customers,id',
//         'amount' => 'required|numeric|min:100',
//         'interest_rate' => 'required|numeric|min:0.1|max:50',
//         'term_months' => 'required|integer|min:1|max:360',
//         'purpose' => 'required|string',
//         'type' => 'required|string',
//         'purpose_description' => 'nullable|string|max:1000',
//     ]);

//     $loan->update($request->all());

//     return redirect()->route('loans.show', $loan->id)->with('success', 'Loan updated successfully!');
// }
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
                'full_name' => $customer->first_name . ' ' . $customer->last_name,
                'email' => $customer->email,
            ];
        });

    // Load loan with customer and guarantor
    $loan->load(['customer', 'guarantor']);

    // Add URL accessors to guarantor if it exists
    if ($loan->guarantor) {
        $loan->guarantor->id_front_url = $loan->guarantor->id_front_path ? asset('storage/' . $loan->guarantor->id_front_path) : null;
        $loan->guarantor->id_back_url = $loan->guarantor->id_back_path ? asset('storage/' . $loan->guarantor->id_back_path) : null;
        $loan->guarantor->passport_picture_url = $loan->guarantor->passport_picture_path ? asset('storage/' . $loan->guarantor->passport_picture_path) : null;
    }

    return Inertia::render('Loans/Edit', [
        'loan' => $loan,
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

    // Validate loan data
    $validated = $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'amount' => 'required|numeric|min:100',
        'interest_rate' => 'required|numeric|min:0.1|max:50',
        'term_months' => 'required|integer|min:1|max:360',
        'purpose' => 'required|string',
        'type' => 'required|string',
        'purpose_description' => 'nullable|string|max:1000',
        'guarantor.full_name' => 'required|string|max:255',
        'guarantor.email' => 'nullable|email|max:255',
        'guarantor.phone' => 'required|string|max:20',
        'guarantor.address' => 'required|string|max:1000',
        'guarantor.relationship' => 'required|string|max:100',
        'guarantor.id_type' => 'required|string|max:50',
        'guarantor.id_number' => 'required|string|max:100',
        'guarantor.id_front' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB - optional for updates
        'guarantor.id_back' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB - optional for updates
        'guarantor.passport_picture' => 'nullable|file|mimes:jpg,jpeg,png|max:5120', // 5MB - optional for updates
    ]);

    try {
        DB::beginTransaction();

        // Update the loan
        $loan->update([
            'customer_id' => $request->customer_id,
            'amount' => $request->amount,
            'interest_rate' => $request->interest_rate,
            'term_months' => $request->term_months,
            'purpose' => $request->purpose,
            'type' => $request->type,
            'purpose_description' => $request->purpose_description,
        ]);

        // Update or create guarantor record
        $guarantorData = [
            'full_name' => $request->input('guarantor.full_name'),
            'email' => $request->input('guarantor.email'),
            'phone' => $request->input('guarantor.phone'),
            'address' => $request->input('guarantor.address'),
            'relationship' => $request->input('guarantor.relationship'),
            'id_type' => $request->input('guarantor.id_type'),
            'id_number' => $request->input('guarantor.id_number'),
        ];

        // Handle file uploads (only update if new files are provided)
        if ($request->hasFile('guarantor.id_front')) {
            // Delete old file if exists
            if ($loan->guarantor && $loan->guarantor->id_front_path) {
                Storage::disk('public')->delete($loan->guarantor->id_front_path);
            }
            $guarantorData['id_front_path'] = $request->file('guarantor.id_front')->store('guarantor-documents', 'public');
        }

        if ($request->hasFile('guarantor.id_back')) {
            // Delete old file if exists
            if ($loan->guarantor && $loan->guarantor->id_back_path) {
                Storage::disk('public')->delete($loan->guarantor->id_back_path);
            }
            $guarantorData['id_back_path'] = $request->file('guarantor.id_back')->store('guarantor-documents', 'public');
        }

        if ($request->hasFile('guarantor.passport_picture')) {
            // Delete old file if exists
            if ($loan->guarantor && $loan->guarantor->passport_picture_path) {
                Storage::disk('public')->delete($loan->guarantor->passport_picture_path);
            }
            $guarantorData['passport_picture_path'] = $request->file('guarantor.passport_picture')->store('guarantor-passports', 'public');
        }

        // Update or create guarantor
        if ($loan->guarantor) {
            $loan->guarantor->update($guarantorData);
        } else {
            // This shouldn't happen if all loans have guarantors, but just in case
            $guarantorData['loan_id'] = $loan->id;
            Guarantor::create($guarantorData);
        }

        DB::commit();

        return redirect()->route('loans.show', $loan->id)->with('success', 'Loan and guarantor information updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Log the error for debugging
        \Log::error('Loan update failed: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Failed to update loan. Please try again.']);
    }
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

        // Calculate loan details
        $loan->calculateLoanDetails();
        
        // Update loan status
        $loan->update([
            'status' => 'disbursed',
            'disbursement_date' => now(),
            'due_date' => now()->addMonths($loan->term_months),
        ]);

        // Generate payment schedule
        $loan->generatePaymentSchedule();

        DB::commit();

        return redirect()->back()->with('success', 'Loan disbursed successfully! Payment schedule generated.');

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


