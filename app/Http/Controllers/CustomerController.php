<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::withCount('loans')
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('id_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

    // Transform the data to include full_name
    $customers->getCollection()->transform(function ($customer) {
        $customer->full_name = $customer->first_name . ' ' . $customer->last_name;
        return $customer;
    });

        return Inertia::render('Customers/index', [
            'customers' => $customers,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Customers/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:-18 years',
            'id_number' => 'required|string|max:50|unique:customers',
            'monthly_income' => 'nullable|numeric|min:0',
            'employment_status' => 'required|in:employed,self-employed,unemployed,retired',
            'employment_details' => 'nullable|string|max:500',
            'credit_score' => 'nullable|integer|min:300|max:850',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer created successfully!');
    }

public function show(Customer $customer)
{
    $customer->load(['loans' => function($query) {
        $query->latest()->take(5);
    }, 'loans.creator']);

    $stats = [
        'total_loans' => $customer->loans()->count(),
        'active_loans' => $customer->loans()->whereIn('status', ['active', 'disbursed'])->count(),
        'total_borrowed' => $customer->loans()->sum('amount'),
        'pending_loans' => $customer->loans()->where('status', 'pending')->count(),
    ];

    return Inertia::render('Customers/Show', [
        'customer' => $customer,
        'stats' => $stats,
        'recentLoans' => $customer->loans,
    ]);
}
    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

public function update(Request $request, Customer $customer)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('customers')->ignore($customer->id),
        ],
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:255',
        'state' => 'required|string|max:255',
        'zip_code' => 'required|string|max:20',
        'date_of_birth' => 'required|date|before:-18 years',
        'id_number' => [
            'required',
            'string',
            'max:50',
            Rule::unique('customers')->ignore($customer->id),
        ],
        'monthly_income' => 'nullable|numeric|min:0',
        'employment_status' => 'required|in:employed,self-employed,unemployed,retired',
        'employment_details' => 'nullable|string|max:500',
        'credit_score' => 'nullable|integer|min:300|max:850',
    ]);

    $customer->update($request->all());

    return redirect()->route('customers.show', $customer->id)->with('success', 'Customer updated successfully!');
}
    public function destroy(Customer $customer)
    {
        if ($customer->loans()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete customer with existing loans.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}