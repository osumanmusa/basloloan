<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Admin: admin@loanapp.com / password');
        $this->command->info('Manager: manager@loanapp.com / password');
    }
}