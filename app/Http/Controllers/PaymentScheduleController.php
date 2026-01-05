<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanPaymentSchedule;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentScheduleController extends Controller
{
    /**
     * Show loans with unsettled payment schedules
     */
    public function index(Request $request)
    {
        try {
            // Get loans that have pending/overdue payment schedules
            $loans = Loan::whereHas('paymentSchedules', function($query) {
                    $query->whereIn('status', ['pending', 'overdue']);
                })
                ->with(['customer', 'paymentSchedules' => function($query) {
                    $query->whereIn('status', ['pending', 'overdue'])
                          ->orderBy('due_date');
                }])
                ->withCount(['paymentSchedules as pending_schedules_count' => function($query) {
                    $query->whereIn('status', ['pending', 'overdue']);
                }])
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
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            // Add computed fields to each loan
            $loans->getCollection()->transform(function ($loan) {
                $loan->total_due_amount = $loan->paymentSchedules->sum('due_amount');
                $loan->next_due_date = $loan->paymentSchedules->min('due_date');
                $loan->overdue_schedules_count = $loan->paymentSchedules
                    ->where('due_date', '<', now())
                    ->whereIn('status', ['pending', 'overdue'])
                    ->count();
                return $loan;
            });

            $stats = [
                'total_loans_with_pending' => Loan::whereHas('paymentSchedules', function($query) {
                    $query->whereIn('status', ['pending', 'overdue']);
                })->count(),
                'total_pending_schedules' => LoanPaymentSchedule::whereIn('status', ['pending', 'overdue'])->count(),
                'overdue_schedules' => LoanPaymentSchedule::where('due_date', '<', now())
                    ->whereIn('status', ['pending', 'overdue'])
                    ->count(),
                'total_due_amount' => LoanPaymentSchedule::whereIn('status', ['pending', 'overdue'])->sum('due_amount'),
            ];

            return Inertia::render('PaymentSchedules/index', [
                'loans' => $loans,
                'stats' => $stats,
                'filters' => $request->only(['search', 'status']),
            ]);

        } catch (\Exception $e) {
            Log::error('PaymentScheduleController index error', [
                'error' => $e->getMessage()
            ]);

            return Inertia::render('PaymentSchedules/index', [
                'loans' => [],
                'stats' => [
                    'total_loans_with_pending' => 0,
                    'total_pending_schedules' => 0,
                    'overdue_schedules' => 0,
                    'total_due_amount' => 0,
                ],
                'filters' => $request->only(['search', 'status']),
            ]);
        }
    }

    /**
     * Show all schedules for a specific loan
     */
    public function loanSchedules(Loan $loan, Request $request)
    {
        try {
            // Load loan with customer
            $loan->load('customer');

            // Get schedules for this loan with filters
            $schedules = $loan->paymentSchedules()
                ->when($request->status, function($query, $status) {
                    $query->where('status', $status);
                })
                ->when($request->date_from, function($query, $dateFrom) {
                    $query->whereDate('due_date', '>=', $dateFrom);
                })
                ->when($request->date_to, function($query, $dateTo) {
                    $query->whereDate('due_date', '<=', $dateTo);
                })
                ->orderBy('due_date')
                ->paginate(20)
                ->withQueryString();

            // Add computed fields
            $schedules->getCollection()->transform(function ($schedule) {
                $schedule->paid_amount = $schedule->payments()->where('status', 'completed')->sum('amount') ?? 0;
                $schedule->remaining_amount = max(0, ($schedule->due_amount - $schedule->paid_amount));
                $schedule->payments_count = $schedule->payments()->count();
                return $schedule;
            });

            $loanStats = [
                'total_schedules' => $loan->paymentSchedules()->count(),
                'pending_schedules' => $loan->paymentSchedules()->whereIn('status', ['pending', 'overdue'])->count(),
                'paid_schedules' => $loan->paymentSchedules()->where('status', 'paid')->count(),
                'total_due_amount' => $loan->paymentSchedules()->whereIn('status', ['pending', 'overdue'])->sum('due_amount'),
            ];

            return Inertia::render('PaymentSchedules/LoanSchedules', [
                'loan' => $loan,
                'schedules' => $schedules,
                'loanStats' => $loanStats,
                'filters' => $request->only(['status', 'date_from', 'date_to']),
            ]);

        } catch (\Exception $e) {
            Log::error('PaymentScheduleController loanSchedules error', [
                'loan_id' => $loan->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('payment-schedules.index')
                ->with('error', 'Error loading loan schedules: ' . $e->getMessage());
        }
    }

    /**
     * Show individual schedule details
     */
public function show($id)
{
    try {
        Log::info('PaymentScheduleController show called', ['schedule_id' => $id]);

        // Find the schedule with ALL relationships loaded
        $schedule = LoanPaymentSchedule::with([
            'loan.customer',
            'payments.receiver'
        ])->find($id);

        if (!$schedule) {
            Log::warning('Payment schedule not found', ['schedule_id' => $id]);
            return redirect()->route('payment-schedules.index')
                ->with('error', 'Payment schedule not found.');
        }

        Log::info('Schedule found', [
            'schedule_id' => $schedule->id,
            'loan_id' => $schedule->loan_id,
            'has_loan' => !is_null($schedule->loan),
            'has_customer' => $schedule->loan && !is_null($schedule->loan->customer)
        ]);

        // If loan relationship failed to load, try to load it manually
        if (!$schedule->loan) {
            Log::warning('Loan relationship not loaded, loading manually', [
                'schedule_id' => $id, 
                'loan_id' => $schedule->loan_id
            ]);
            
            $schedule->loan = Loan::with('customer')->find($schedule->loan_id);
            
            if (!$schedule->loan) {
                Log::error('Loan not found for schedule', [
                    'schedule_id' => $id,
                    'loan_id' => $schedule->loan_id
                ]);
            }
        }

        // Add computed fields
        $schedule->paid_amount = $schedule->payments()->where('status', 'completed')->sum('amount') ?? 0;
        $schedule->remaining_amount = max(0, ($schedule->due_amount - $schedule->paid_amount));
        $schedule->payments_count = $schedule->payments()->count();

        Log::info('PaymentScheduleController show returning data', [
            'schedule_id' => $schedule->id,
            'has_loan' => !is_null($schedule->loan),
            'loan_id' => $schedule->loan_id,
            'customer_name' => $schedule->loan && $schedule->loan->customer 
                ? $schedule->loan->customer->first_name . ' ' . $schedule->loan->customer->last_name
                : 'No customer',
            'computed_fields' => [
                'paid_amount' => $schedule->paid_amount,
                'remaining_amount' => $schedule->remaining_amount,
                'payments_count' => $schedule->payments_count
            ]
        ]);

        return Inertia::render('PaymentSchedules/Show', [
            'schedule' => $schedule,
        ]);

    } catch (\Exception $e) {
        Log::error('PaymentScheduleController show error', [
            'schedule_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->route('payment-schedules.index')
            ->with('error', 'Error loading payment schedule: ' . $e->getMessage());
    }
}
}