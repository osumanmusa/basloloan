<?php

namespace App\Exports;

use App\Models\LoanPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoanPaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'Payment ID',
            'Loan ID',
            'Customer Name',
            'Amount',
            'Principal',
            'Interest',
            'Payment Date',
            'Due Date',
            'Status',
            'Payment Method',
            'Reference Number',
            'Received By',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->loan_id,
            $payment->loan->customer->first_name . ' ' . $payment->loan->customer->last_name,
            $payment->amount,
            $payment->principal_amount,
            $payment->interest_amount,
            $payment->payment_date?->format('Y-m-d'),
            $payment->due_date?->format('Y-m-d'),
            ucfirst($payment->status),
            ucfirst(str_replace('_', ' ', $payment->payment_method)),
            $payment->reference_number,
            $payment->receiver->name ?? 'System',
        ];
    }
}