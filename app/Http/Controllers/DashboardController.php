<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Customer;
use App\Models\LoanPayment;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic stats
        $stats = [
            'total_loans' => Loan::count(),
            'total_customers' => Customer::count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'overdue_payments' => LoanPayment::where('status', 'late')->count(),
            'total_portfolio' => Loan::whereIn('status', ['approved', 'disbursed', 'active'])->sum('amount'),
            'active_loans' => Loan::whereIn('status', ['disbursed', 'active'])->count(),
            'monthly_revenue' => LoanPayment::where('status', 'completed')
                ->whereMonth('payment_date', now()->month)
                ->sum('amount'),
            'default_rate' => $this->calculateDefaultRate(),
        ];

        // Chart data
        $charts = [
            'loanStatus' => $this->getLoanStatusDistribution(),
            'monthlyDisbursements' => $this->getMonthlyDisbursements(),
        ];

        // Recent loans
        $recentLoans = Loan::with('customer')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'amount' => $loan->amount,
                    'status' => $loan->status,
                    'customer' => [
                        'full_name' => $loan->customer->first_name . ' ' . $loan->customer->last_name,
                    ],
                ];
            });

        // Upcoming payments (simplified for now)
        $upcomingPayments = LoanPayment::with('loan.customer')
            ->where('status', 'pending')
            ->whereDate('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'due_date' => $payment->due_date->format('M d, Y'),
                    'loan' => [
                        'id' => $payment->loan->id,
                        'customer' => [
                            'full_name' => $payment->loan->customer->first_name . ' ' . $payment->loan->customer->last_name,
                        ],
                    ],
                ];
            });

        // Performance metrics (simplified)
        $performanceMetrics = [
            'collection_rate' => $this->calculateCollectionRate(),
            'average_loan_size' => Loan::avg('amount'),
            'customer_satisfaction' => 95, // Placeholder
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'charts' => $charts,
            'recentLoans' => $recentLoans,
            'upcomingPayments' => $upcomingPayments,
            'performanceMetrics' => $performanceMetrics,
        ]);
    }

    private function getLoanStatusDistribution()
    {
        $statuses = Loan::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        $colorMap = [
            'pending' => '#fbbf24', // yellow
            'approved' => '#10b981', // green
            'rejected' => '#ef4444', // red
            'disbursed' => '#3b82f6', // blue
            'active' => '#8b5cf6', // purple
            'completed' => '#6b7280', // gray
            'defaulted' => '#dc2626', // red
        ];

        return $statuses->map(function ($status) use ($colorMap) {
            return [
                'name' => ucfirst(str_replace('_', ' ', $status->status)),
                'value' => $status->count,
                'itemStyle' => [
                    'color' => $colorMap[$status->status] ?? '#9ca3af'
                ]
            ];
        })->toArray();
    }

    private function getMonthlyDisbursements()
    {
        // Get disbursements for the last 6 months
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $disbursedAmount = Loan::where('status', 'disbursed')
                ->whereYear('disbursement_date', $month->year)
                ->whereMonth('disbursement_date', $month->month)
                ->sum('amount');

            $data[] = [
                'name' => $month->format('M Y'),
                'value' => $disbursedAmount ?: 0
            ];
        }

        return $data;
    }

    private function calculateDefaultRate()
    {
        $totalLoans = Loan::count();
        $defaultedLoans = Loan::where('status', 'defaulted')->count();

        return $totalLoans > 0 ? round(($defaultedLoans / $totalLoans) * 100, 2) : 0;
    }

    private function calculateCollectionRate()
    {
        $totalDue = LoanPayment::where('status', 'completed')->sum('amount') + 
                    LoanPayment::where('status', 'pending')->sum('amount');
        $collected = LoanPayment::where('status', 'completed')->sum('amount');

        return $totalDue > 0 ? round(($collected / $totalDue) * 100, 2) : 100;
    }

    // Add these additional helper methods that might be needed

    private function getPortfolioGrowth()
    {
        // Simplified portfolio growth calculation
        $currentMonth = Loan::whereIn('status', ['approved', 'disbursed', 'active'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $previousMonth = Loan::whereIn('status', ['approved', 'disbursed', 'active'])
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        return $previousMonth > 0 ? round((($currentMonth - $previousMonth) / $previousMonth) * 100, 2) : 0;
    }

    private function getRevenueTrends()
    {
        $revenueData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = LoanPayment::where('status', 'completed')
                ->whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');

            $revenueData[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue ?: 0
            ];
        }

        return $revenueData;
    }

    private function getCustomerAcquisition()
    {
        $acquisitionData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $newCustomers = Customer::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $acquisitionData[] = [
                'month' => $month->format('M Y'),
                'customers' => $newCustomers
            ];
        }

        return $acquisitionData;
    }
}