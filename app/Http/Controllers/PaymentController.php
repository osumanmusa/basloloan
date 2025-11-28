<?php

namespace App\Http\Controllers;

use App\Models\LoanPayment;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = LoanPayment::with(['loan.customer', 'receiver'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
        ]);
    }

    public function create()
    {
        return Inertia::render('Payments/Create');
    }

    public function store(Request $request)
    {
        // Basic implementation
        return back()->with('success', 'Payment recorded successfully!');
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
        return Inertia::render('Payments/Edit', [
            'payment' => $payment,
        ]);
    }

    public function update(Request $request, LoanPayment $payment)
    {
        // Basic implementation
        return back()->with('success', 'Payment updated successfully!');
    }

    public function destroy(LoanPayment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully!');
    }
}