<?php

namespace Tests\Feature;

use App\Models\PrintingService;
use App\Models\User;
use App\Notifications\ServiceOrderCreatedNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ServiceOrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_owner_is_notified_when_customer_requests_a_service(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $customer = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'printing',
            'quantity' => 2,
            'deadline' => now()->addWeek()->format('Y-m-d'),
            'notes' => 'Please rush',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo(
            $owner,
            ServiceOrderCreatedNotification::class,
            function (ServiceOrderCreatedNotification $notification) use ($owner, $service) {
                $data = $notification->toArray($owner);

                return $data['event_type'] === 'service_order_created'
                    && $data['title'] === 'New Service Order'
                    && str_contains($data['message'], $service->name)
                    && $data['url'] === route('owner.jobs.show', $notification->job);
            }
        );
    }

    public function test_notification_is_persisted_to_database_for_owner(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create(['is_active' => true]);

        $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'printing',
            'quantity' => 1,
            'deadline' => now()->addWeek()->format('Y-m-d'),
        ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $owner->id,
            'notifiable_type' => User::class,
        ]);

        $this->assertSame(
            1,
            $owner->fresh()->unreadNotifications
                ->where('data.event_type', 'service_order_created')
                ->count()
        );
    }

    public function test_new_order_appears_in_owner_right_panel(): void
    {
        $owner = User::factory()->create([
            'first_name' => 'Owner',
            'last_name' => 'Test',
        ]);
        $owner->assignRole('owner');

        $customer = User::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Cust',
        ]);
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create([
            'is_active' => true,
            'name' => 'Premium Tarpaulin',
        ]);

        $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'printing',
            'quantity' => 3,
            'deadline' => now()->addWeek()->format('Y-m-d'),
        ])->assertRedirect();

        $response = $this->actingAs($owner)->get(route('owner.dashboard'));

        $response->assertOk();
        $response->assertSeeText('New orders');
        $response->assertSeeText('Premium Tarpaulin');
        $response->assertSeeText('Customer submitted a new order');
    }

    public function test_no_notification_when_no_owner_exists(): void
    {
        Notification::fake();

        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $service = PrintingService::factory()->create(['is_active' => true]);

        $response = $this->actingAs($customer)->post(route('customer.request-service'), [
            'service_id' => $service->id,
            'service_type' => 'printing',
            'quantity' => 1,
            'deadline' => now()->addWeek()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        Notification::assertNothingSent();
    }
}
