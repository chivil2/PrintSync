<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an OWNER
        $owner = User::updateOrCreate(
            ['email' => 'owner@printsync.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Owner',
                'phone' => '1234567890',
                'password' => bcrypt('password'),
                // Owner-specific fields
                'company_name' => 'PrintSync Solutions',
                'tax_id' => 'TAX-123456789',
                'business_address' => '123 Business Ave, Metro Manila, Philippines',
            ]
        );
        $owner->assignRole('owner');

        // Create an EMPLOYEE (Printing Staff)
        $printingStaff = User::updateOrCreate(
            ['email' => 'jane@printsync.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Printer',
                'phone' => '1234567891',
                'password' => bcrypt('password'),
                'title' => 'printing_staff', // Title field for employee classification
                // Employee-specific fields
                'employee_id' => 'EMP-001',
                'hire_date' => '2024-01-15',
                'specialization' => 'printing_staff',
                'hourly_rate' => 150.00,
                'employee_status' => 'active',
            ]
        );
        $printingStaff->assignRole('employee');

        // Create an EMPLOYEE (Technical Staff)
        $technicalStaff = User::updateOrCreate(
            ['email' => 'bob@printsync.com'],
            [
                'first_name' => 'Bob',
                'last_name' => 'Technician',
                'phone' => '1234567892',
                'password' => bcrypt('password'),
                'title' => 'technical_staff', // Title field for employee classification
                // Employee-specific fields
                'employee_id' => 'EMP-002',
                'hire_date' => '2024-02-01',
                'specialization' => 'technical_staff',
                'hourly_rate' => 200.00,
                'employee_status' => 'active',
            ]
        );
        $technicalStaff->assignRole('employee');

        // Create a CUSTOMER
        $customer = User::updateOrCreate(
            ['email' => 'alice@example.com'],
            [
                'first_name' => 'Alice',
                'last_name' => 'Customer',
                'phone' => '1234567893',
                'password' => bcrypt('password'),
                'title' => null, // Customers don't have a title
                // Customer-specific fields
                'customer_id' => 'CUST-001',
                'billing_address' => '456 Customer St, Quezon City, Philippines',
                'shipping_address' => '456 Customer St, Quezon City, Philippines',
                'credit_limit' => 50000.00,
                'preferred_payment_method' => 'credit_card',
            ]
        );
        $customer->assignRole('customer');

        $this->command->info('Demo users created:');
        $this->command->info('- Owner: owner@printsync.com (password)');
        $this->command->info('- Printing Staff: jane@printsync.com (password)');
        $this->command->info('- Technical Staff: bob@printsync.com (password)');
        $this->command->info('- Customer: alice@example.com (password)');
    }
}
