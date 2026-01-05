<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanPaymentSchedule;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions if they don't exist
        $permissions = [
            // Loan Management
            'view-loans', 'create-loans', 'edit-loans', 'delete-loans', 
            'approve-loans', 'reject-loans', 'disburse-loans',
            
            // Customer Management
            'view-customers', 'create-customers', 'edit-customers', 'delete-customers',
            
            // Payment Management
            'view-payments', 'create-payments', 'edit-payments', 'verify-payments',
            
            // User Management
            'view-users', 'create-users', 'edit-users', 'delete-users', 'assign-roles',
            
            // Role & Permission Management
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
            'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions', 'assign-permissions',
            
            // Reports & Analytics
            'view-reports', 'export-data', 'view-analytics',
            
            // System Settings
            'manage-settings', 'backup-data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create Roles and Assign Permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $loanManager = Role::firstOrCreate(['name' => 'loan-manager', 'guard_name' => 'web']);
        $loanManager->syncPermissions([
            'view-loans', 'create-loans', 'edit-loans', 'approve-loans', 'reject-loans', 'disburse-loans',
            'view-customers', 'create-customers', 'edit-customers',
            'view-payments', 'create-payments', 'edit-payments',
            'view-reports', 'export-data',
        ]);

        $loanOfficer = Role::firstOrCreate(['name' => 'loan-officer', 'guard_name' => 'web']);
        $loanOfficer->syncPermissions([
            'view-loans', 'create-loans', 'edit-loans',
            'view-customers', 'create-customers', 'edit-customers',
            'view-payments', 'create-payments',
        ]);

        $customerService = Role::firstOrCreate(['name' => 'customer-service', 'guard_name' => 'web']);
        $customerService->syncPermissions([
            'view-loans', 'view-customers', 'view-payments', 'create-payments',
        ]);

        $auditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $auditor->syncPermissions([
            'view-loans', 'view-customers', 'view-payments', 'view-reports', 'export-data',
        ]);

        // Create admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@loanapp.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        // Create loan manager user if it doesn't exist
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@loanapp.com'],
            [
                'name' => 'Loan Manager',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $managerUser->assignRole('loan-manager');


        // Create a test user (uncommented and fixed)
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Add password
            'email_verified_at' => now(),
        ]);       
        
        $user->assignRole('admin');


        // Create a test customer
        $customer = Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'date_of_birth' => '1985-05-15',
            'id_number' => 'ID123456789',
            'monthly_income' => 5000.00,
            'employment_status' => 'employed',
            'employment_details' => 'Software Developer at Tech Corp',
            'credit_score' => 750,
        ]);

        // Create a test loan
        $loan = Loan::create([
            'customer_id' => $customer->id,
            'created_by' => $user->id, // Now this will work
            'amount' => 10000.00,
            'interest_rate' => 5.0,
            'term_months' => 12,
            'purpose' => 'debt_consolidation',
            'type' => 'personal',
            'status' => 'active',
            'monthly_payment' => 856.07,
            'total_amount' => 10272.84,
            'remaining_balance' => 10000.00,
            'disbursement_date' => now()->subDays(30),
            'due_date' => now()->addMonths(12),
        ]);

        // Create payment schedules for the loan
        for ($i = 1; $i <= 12; $i++) {
            LoanPaymentSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_amount' => 856.07,
                'principal_amount' => 814.40,
                'interest_amount' => 41.67,
                'due_date' => now()->addMonths($i),
                'status' => 'pending',
                'notes' => "Monthly installment #{$i}",
            ]);
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('Admin User: admin@example.com / password: password');
        $this->command->info('Customer: John Doe');
        $this->command->info('Loan with 12 payment schedules created.');
        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Admin: admin@loanapp.com / password');
        $this->command->info('Manager: manager@loanapp.com / password');
    }
}