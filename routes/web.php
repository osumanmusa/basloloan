<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LoanCalculatorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BulkPaymentController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Loans with advanced actions
    Route::resource('loans', LoanController::class);
    Route::put('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::put('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::put('/loans/{loan}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');
    Route::post('/loans/calculate', [LoanController::class, 'calculate'])->name('loans.calculate');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Payments
    Route::resource('payments', PaymentController::class);
    Route::put('/payments/{payment}/complete', [PaymentController::class, 'complete'])->name('payments.complete');

// Bulk payments (placeholder for now)
Route::get('/payments/bulk/create', [BulkPaymentController::class, 'create'])->name('payments.bulk.create');
Route::post('/payments/bulk', [BulkPaymentController::class, 'store'])->name('payments.bulk.store');
// Payments
Route::post('/payments/{payment}/process-scheduled', [PaymentController::class, 'processScheduledPayment'])->name('payments.process-scheduled');
Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
Route::get('/payments/{payment}/export-pdf', [PaymentController::class, 'exportPdf'])->name('payments.export.pdf');
Route::get('/loans/{loan}/payments', [PaymentController::class, 'loanPayments'])->name('loans.payments');
Route::get('/loans/{loan}/export-payments', [PaymentController::class, 'exportLoanPayments'])->name('loans.export.payments');
    // User Management
    Route::resource('users', UserController::class)->middleware('permission:view-users');
    Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');

    // Role Management
    Route::resource('roles', RoleController::class)->middleware('permission:view-roles');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/analytics', [ReportController::class, 'analytics'])->name('reports.analytics');

    // Loan Calculator
    Route::get('/calculator', [LoanCalculatorController::class, 'index'])->name('calculator.index');
    Route::post('/calculator/calculate', [LoanCalculatorController::class, 'calculate'])->name('calculator.calculate');

    
    // Role Management (Admin only)

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Add this to your routes
Route::get('/payments/bulk', [BulkPaymentController::class, 'create'])->name('payments.bulk');
    // Bulk Payments
    Route::get('/payments/bulk/create', [BulkPaymentController::class, 'create'])->name('payments.bulk.create');
    Route::post('/payments/bulk', [BulkPaymentController::class, 'store'])->name('payments.bulk.store');
    Route::post('/payments/bulk/process', [BulkPaymentController::class, 'processBatch'])->name('payments.bulk.process');
});

require __DIR__.'/auth.php';
