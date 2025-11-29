<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Add this route for scheduled payments
Route::get('/loans/{loan}/scheduled-payments', function ($loanId) {
    $loan = \App\Models\Loan::with(['payments' => function($query) {
        $query->where('status', 'pending')->orderBy('due_date');
    }])->find($loanId);

    if (!$loan) {
        return response()->json([], 404);
    }

    return $loan->payments->map(function($payment) {
        return [
            'id' => $payment->id,
            'amount' => $payment->amount,
            'due_date' => $payment->due_date,
            'principal_amount' => $payment->principal_amount,
            'interest_amount' => $payment->interest_amount,
            'status' => $payment->status,
        ];
    });
});