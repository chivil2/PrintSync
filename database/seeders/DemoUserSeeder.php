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
        $owner = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Owner',
            'email' => 'owner@printsync.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);
        $owner->assignRole('owner');

        // Create an EMPLOYEE (Printing Staff)
        $printingStaff = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Printer',
            'email' => 'jane@printsync.com',
            'phone' => '1234567891',
            'password' => bcrypt('password'),
            'title' => 'printing_staff', // Title field for employee classification
        ]);
        $printingStaff->assignRole('employee');

        // Create an EMPLOYEE (Technical Staff)
        $technicalStaff = User::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Technician',
            'email' => 'bob@printsync.com',
            'phone' => '1234567892',
            'password' => bcrypt('password'),
            'title' => 'technical_staff', // Title field for employee classification
        ]);
        $technicalStaff->assignRole('employee');

        // Create a CUSTOMER
        $customer = User::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Customer',
            'email' => 'alice@example.com',
            'phone' => '1234567893',
            'password' => bcrypt('password'),
            'title' => null, // Customers don't have a title
        ]);
        $customer->assignRole('customer');

        $this->command->info('Demo users created:');
        $this->command->info('- Owner: owner@printsync.com (password)');
        $this->command->info('- Printing Staff: jane@printsync.com (password)');
        $this->command->info('- Technical Staff: bob@printsync.com (password)');
        $this->command->info('- Customer: alice@example.com (password)');
    }
}
