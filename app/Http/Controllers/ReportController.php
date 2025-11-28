<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Customer;
use App\Models\LoanPayment;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'loan_type', 'status']);

        $reports = [
            'loan_performance' => $this->getLoanPerformanceReport($filters),
            'customer_analysis' => $this->getCustomerAnalysisReport($filters),
            'payment_collection' => $this->getPaymentCollectionReport($filters),
            'risk_analysis' => $this->getRiskAnalysisReport($filters),
        ];

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'filters' => $filters,
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'loan_type', 'status']);
        
        // Generate Excel/PDF report
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    public function analytics()
    {
        $analytics = [
            'portfolio_growth' => $this->getPortfolioGrowth(),
            'default_rates' => $this->getDefaultRates(),
            'customer_acquisition' => $this->getCustomerAcquisition(),
            'revenue_trends' => $this->getRevenueTrends(),
        ];

        return Inertia::render('Reports/Analytics', [
            'analytics' => $analytics,
        ]);
    }

    private function getLoanPerformanceReport($filters)
    {
        $query = Loan::query();

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_loans' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'average_loan_size' => $query->avg('amount'),
            'approval_rate' => $this->calculateApprovalRate($query),
        ];
    }

    private function calculateApprovalRate($query)
    {
        $total = $query->count();
        $approved = clone $query;
        $approved = $approved->whereIn('status', ['approved', 'disbursed', 'active', 'completed'])->count();
        
        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }

    // ... other report methods ...
}