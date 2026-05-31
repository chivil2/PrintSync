<?php

namespace Database\Seeders;

use App\Models\TechnicalService;
use Illuminate\Database\Seeder;

class TechnicalServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Installation',
                'description' => 'Professional installation services for signage, displays, and equipment. Expert technicians ensure proper setup.',
                'price' => 1500.00,
                'is_active' => true,
            ],
            [
                'name' => 'Repair',
                'description' => 'Repair services for damaged signage, equipment, and printing materials. Quick turnaround time.',
                'price' => 1000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Regular maintenance services for printing equipment and installed signage. Preventive care to extend equipment life.',
                'price' => 800.00,
                'is_active' => true,
            ],
            [
                'name' => 'Consultation',
                'description' => 'Expert printing and signage consultation. Design advice, material selection, and project planning services.',
                'price' => 500.00,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            TechnicalService::create($service);
        }
    }
}
