<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_guests_cannot_view_reports(): void
    {
        $this->get(route('owner.reports'))->assertRedirect(route('login'));
    }

    public function test_non_owners_cannot_view_reports(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $this->actingAs($employee)
            ->get(route('owner.reports'))
            ->assertForbidden();
    }

    public function test_owner_can_view_inventory_reports(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $a4 = Inventory::create([
            'name' => 'A4 Bond Paper',
            'sku' => 'PAP-A4',
            'description' => 'Standard A4 paper',
            'quantity' => 1000,
            'min_stock_level' => 200,
            'unit_price' => 0.50,
            'unit' => 'sheet',
            'supplier' => 'Paper Co',
            'location' => 'Shelf A1',
        ]);
        $a4->updateStatus();

        $toner = Inventory::create([
            'name' => 'Toner Cartridge',
            'sku' => 'INK-TON-01',
            'description' => 'Black toner',
            'quantity' => 0,
            'min_stock_level' => 5,
            'unit_price' => 250.00,
            'unit' => 'piece',
            'supplier' => null,
            'location' => 'Cabinet B',
        ]);
        $toner->updateStatus();

        $response = $this->actingAs($owner)->get(route('owner.reports'));

        $response->assertOk();
        $response->assertViewIs('owner.reports');
        $response->assertViewHasAll([
            'totals',
            'statusBreakdown',
            'topByQuantity',
            'topByValue',
            'topByUnitPrice',
            'stockLevelDistribution',
            'lowStockItems',
            'supplierBreakdown',
        ]);

        $totals = $response->viewData('totals');
        $this->assertSame(2, $totals['skus']);
        $this->assertSame(1000, $totals['quantity']);
        $this->assertEqualsWithDelta(1000 * 0.50, $totals['value'], 0.01);
        $this->assertSame(0, $totals['low_stock']);
        $this->assertSame(1, $totals['out_of_stock']);

        $status = $response->viewData('statusBreakdown');
        $this->assertSame(1, $status['in_stock']);
        $this->assertSame(0, $status['low_stock']);
        $this->assertSame(1, $status['out_of_stock']);

        $topByQuantity = $response->viewData('topByQuantity');
        $items = $topByQuantity->toArray();
        $firstQty = $topByQuantity->first()['quantity'] ?? 'missing';
        $this->assertSame('A4 Bond Paper', $topByQuantity->first()['name']);
        $this->assertSame(1000, $firstQty, 'topByQuantity: '.json_encode($items));

        $topByValue = $response->viewData('topByValue');
        $this->assertSame('A4 Bond Paper', $topByValue->first()['name']);
        $this->assertEqualsWithDelta(500.00, $topByValue->first()['value'], 0.01);

        $distribution = $response->viewData('stockLevelDistribution');
        $this->assertCount(6, $distribution);
        $this->assertSame(2, array_sum(array_column($distribution, 'count')));

        $suppliers = $response->viewData('supplierBreakdown');
        $this->assertCount(2, $suppliers);
        $this->assertSame('Paper Co', $suppliers->first()['supplier']);
        $this->assertSame('Unspecified', $suppliers->last()['supplier']);
    }

    public function test_low_stock_items_are_listed_for_attention(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $glue = Inventory::create([
            'name' => 'Glue Stick',
            'sku' => 'OFF-GLUE',
            'quantity' => 4,
            'min_stock_level' => 25,
            'unit_price' => 12.00,
            'unit' => 'piece',
        ]);
        $glue->updateStatus();

        $response = $this->actingAs($owner)->get(route('owner.reports'));

        $response->assertOk();
        $response->assertSeeText('Glue Stick');

        $lowStock = $response->viewData('lowStockItems');
        $this->assertCount(1, $lowStock);
        $this->assertSame('low_stock', $lowStock->first()->status);
    }
}
