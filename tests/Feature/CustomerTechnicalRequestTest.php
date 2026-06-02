<?php

namespace Tests\Feature;

use App\Models\ServiceJob;
use App\Models\TechnicalService;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTechnicalRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_customer_can_submit_technical_support_request(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $service = TechnicalService::factory()->create([
            'is_active' => true,
            'name' => 'Laptop Repair',
            'price' => 750.00,
        ]);

        $preferredAt = now()->addDays(2)->format('Y-m-d H:i:s');

        $response = $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'technical',
            'problem_description' => 'Laptop won\'t boot, blue screen on startup with error code 0xC000021A.',
            'priority' => 'urgent',
            'preferred_at' => $preferredAt,
            'contact_preference' => 'phone',
            'notes' => 'Available after 5pm on weekdays.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('service_jobs', [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'type' => 'technical',
            'service_type' => 'technical_service',
            'status' => 'pending',
        ]);

        $job = ServiceJob::where('customer_id', $customer->id)->first();
        $this->assertNotNull($job);
        $this->assertEquals([
            'problem_description' => 'Laptop won\'t boot, blue screen on startup with error code 0xC000021A.',
            'priority' => 'urgent',
            'preferred_at' => $preferredAt,
            'contact_preference' => 'phone',
        ], $job->technical_details);
        $this->assertNull($job->deadline);
        $this->assertEquals('Available after 5pm on weekdays.', $job->notes);
    }

    public function test_technical_request_requires_problem_description(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $service = TechnicalService::factory()->create(['is_active' => true]);

        $response = $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'technical',
            'problem_description' => 'short',
            'priority' => 'standard',
            'preferred_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'contact_preference' => 'email',
        ]);

        $response->assertSessionHasErrors('problem_description');
        $this->assertDatabaseCount('service_jobs', 0);
    }

    public function test_technical_request_requires_valid_priority(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $service = TechnicalService::factory()->create(['is_active' => true]);

        $response = $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'technical',
            'problem_description' => 'My computer is making a strange grinding noise from the fan area.',
            'priority' => 'yesterday',
            'preferred_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'contact_preference' => 'email',
        ]);

        $response->assertSessionHasErrors('priority');
        $this->assertDatabaseCount('service_jobs', 0);
    }

    public function test_technical_request_preferred_at_must_be_in_the_future(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $service = TechnicalService::factory()->create(['is_active' => true]);

        $response = $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'technical',
            'problem_description' => 'Need help migrating data from old hard drive to new SSD.',
            'priority' => 'standard',
            'preferred_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'contact_preference' => 'sms',
        ]);

        $response->assertSessionHasErrors('preferred_at');
        $this->assertDatabaseCount('service_jobs', 0);
    }
}
