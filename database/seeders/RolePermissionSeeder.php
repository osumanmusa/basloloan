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

        // Create Permissions
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
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and Assign Permissions
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        $loanManager = Role::create(['name' => 'loan-manager', 'guard_name' => 'web']);
        $loanManager->givePermissionTo([
            'view-loans', 'create-loans', 'edit-loans', 'approve-loans', 'reject-loans', 'disburse-loans',
            'view-customers', 'create-customers', 'edit-customers',
            'view-payments', 'create-payments', 'edit-payments',
            'view-reports', 'export-data',
        ]);

        $loanOfficer = Role::create(['name' => 'loan-officer', 'guard_name' => 'web']);
        $loanOfficer->givePermissionTo([
            'view-loans', 'create-loans', 'edit-loans',
            'view-customers', 'create-customers', 'edit-customers',
            'view-payments', 'create-payments',
        ]);

        $customerService = Role::create(['name' => 'customer-service', 'guard_name' => 'web']);
        $customerService->givePermissionTo([
            'view-loans', 'view-customers', 'view-payments', 'create-payments',
        ]);

        $auditor = Role::create(['name' => 'auditor', 'guard_name' => 'web']);
        $auditor->givePermissionTo([
            'view-loans', 'view-customers', 'view-payments', 'view-reports', 'export-data',
        ]);

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@loanapp.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        // Create loan manager user
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