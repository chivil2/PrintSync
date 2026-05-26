<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Service job permissions (existing)
            'view_assigned_service_jobs',
            'update_service_job_status',
            'add_service_job_notes',
            'create_service_requests',
            'view_own_orders',
            'cancel_own_orders',
            'upload_service_files',
            'manage_all_service_jobs',
            'assign_service_jobs',
            'manage_pricing',
            'manage_users',
            // Quote permissions (new)
            'manage_all_quotes',
            'view_assigned_quotes',
            'update_quote_status',
            'request_quotes',
            'view_own_quotes',
            'place_orders',
            // Analytics permissions (new)
            'view_analytics',
            // Profile permissions (existing)
            'view_own_profile',
            'update_own_profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view_assigned_service_jobs',
            'update_service_job_status',
            'add_service_job_notes',
            'view_assigned_quotes',
            'update_quote_status',
            'view_own_profile',
            'update_own_profile',
        ]);

        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'create_service_requests',
            'view_own_orders',
            'cancel_own_orders',
            'view_own_profile',
            'update_own_profile',
            'upload_service_files',
            'request_quotes',
            'view_own_quotes',
            'place_orders',
        ]);

        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->givePermissionTo([
            'manage_all_service_jobs',
            'assign_service_jobs',
            'manage_pricing',
            'manage_users',
            'manage_all_quotes',
            'view_analytics',
            'view_own_profile',
            'update_own_profile',
        ]);
    }
}
