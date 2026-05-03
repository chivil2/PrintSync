<?php

namespace Database\Seeders;

use App\Models\PrintingService;
use Illuminate\Database\Seeder;

class PrintingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Signage',
                'description' => 'Professional signage printing for businesses and events. High-quality materials and vibrant colors.',
                'price' => 500.00,
                'is_active' => true,
            ],
            [
                'name' => 'Tarpaulin',
                'description' => 'Durable tarpaulin printing for outdoor advertising and events. Weather-resistant and long-lasting.',
                'price' => 800.00,
                'is_active' => true,
            ],
            [
                'name' => 'Invitations',
                'description' => 'Custom invitation printing for weddings, birthdays, and special occasions. Premium paper quality.',
                'price' => 150.00,
                'is_active' => true,
            ],
            [
                'name' => 'Brochure',
                'description' => 'Professional brochure printing for marketing and promotional materials. Various sizes available.',
                'price' => 200.00,
                'is_active' => true,
            ],
            [
                'name' => 'T-Shirts',
                'description' => 'Custom t-shirt printing with your designs. High-quality prints that last.',
                'price' => 300.00,
                'is_active' => true,
            ],
            [
                'name' => 'Mugs',
                'description' => 'Personalized mug printing for gifts and promotional items. Dishwasher safe.',
                'price' => 250.00,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            PrintingService::create($service);
        }
    }
}
