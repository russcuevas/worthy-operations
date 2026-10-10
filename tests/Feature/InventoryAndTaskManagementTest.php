<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\StaffTask;
use App\Models\StorageLocation;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAndTaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $staffJuan;
    protected $staffMaria;
    protected $warehouse;
    protected $rackA;
    protected $rackB;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Acosta',
            'username' => 'admin_test',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staffJuan = User::factory()->create([
            'name' => 'Juan Staff',
            'username' => 'juan_test',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $this->staffMaria = User::factory()->create([
            'name' => 'Maria Staff',
            'username' => 'maria_test',
            'role' => 'staff',
            'status' => 'active',
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Biaan Central Warehouse',
            'code' => 'WH-BIAAN',
            'status' => 'active',
        ]);

        $this->rackA = StorageLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Freezer 2 / Rack A',
            'type' => 'Freezer',
            'status' => 'active',
        ]);

        $this->rackB = StorageLocation::create([
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Freezer 2 / Rack B',
            'type' => 'Freezer',
            'status' => 'active',
        ]);

        $this->item = Item::create([
            'sku' => 'SKU-TEST-001',
            'name' => 'Australian Ribeye Test',
            'uom' => 'kg',
            'cost_price' => 800.00,
            'min_threshold' => 10,
            'status' => 'active',
        ]);
    }

    public function test_stock_in_transfer_and_stock_out_flow(): void
    {
        // 1. Stock In: 100 units into Rack A
        $response = $this->actingAs($this->admin)->post(route('admin.stock.in'), [
            'item_id' => $this->item->id,
            'quantity' => 100,
            'destination_location_id' => $this->rackA->id,
            'transaction_date' => now()->toDateTimeString(),
            'reference_number' => 'PO-1001',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(100, $this->item->fresh()->total_quantity);
        $this->assertEquals(100, ItemLocation::where('item_id', $this->item->id)->where('storage_location_id', $this->rackA->id)->value('quantity'));

        // 2. Transfer: 40 units from Rack A to Rack B
        $transferResponse = $this->actingAs($this->admin)->post(route('admin.stock.transfer'), [
            'item_id' => $this->item->id,
            'quantity' => 40,
            'source_location_id' => $this->rackA->id,
            'destination_location_id' => $this->rackB->id,
            'transaction_date' => now()->toDateTimeString(),
        ]);

        $transferResponse->assertSessionHasNoErrors();
        // Total stock remains 100
        $this->assertEquals(100, $this->item->fresh()->total_quantity);
        // Rack A has 60, Rack B has 40
        $this->assertEquals(60, ItemLocation::where('item_id', $this->item->id)->where('storage_location_id', $this->rackA->id)->value('quantity'));
        $this->assertEquals(40, ItemLocation::where('item_id', $this->item->id)->where('storage_location_id', $this->rackB->id)->value('quantity'));

        // 3. Stock Out: Release 25 units from Rack B
        $outResponse = $this->actingAs($this->admin)->post(route('admin.stock.out'), [
            'item_id' => $this->item->id,
            'quantity' => 25,
            'source_location_id' => $this->rackB->id,
            'recipient_customer_department' => 'Customer Kitchen',
            'transaction_date' => now()->toDateTimeString(),
        ]);

        $outResponse->assertSessionHasNoErrors();
        // Total stock becomes 75
        $this->assertEquals(75, $this->item->fresh()->total_quantity);
        $this->assertEquals(15, ItemLocation::where('item_id', $this->item->id)->where('storage_location_id', $this->rackB->id)->value('quantity'));

        // 4. Overdrawing stock test (attempting to withdraw 20 from Rack B when only 15 is available)
        $invalidOut = $this->actingAs($this->admin)->post(route('admin.stock.out'), [
            'item_id' => $this->item->id,
            'quantity' => 20,
            'source_location_id' => $this->rackB->id,
            'transaction_date' => now()->toDateTimeString(),
        ]);
        // Stock should remain 15 and not go negative
        $this->assertEquals(15, ItemLocation::where('item_id', $this->item->id)->where('storage_location_id', $this->rackB->id)->value('quantity'));
    }

    public function test_staff_tasks_security_and_verification_flow(): void
    {
        // Admin creates task for Juan
        $task = StaffTask::create([
            'task_code' => 'TSK-TEST-001',
            'title' => 'Inspect Meat Cold Storage',
            'assigned_by' => $this->admin->id,
            'assigned_to' => $this->staffJuan->id,
            'priority' => 'high',
            'status' => 'not_started',
            'due_datetime' => now()->addHours(4),
        ]);

        // Juan starts task
        $startRes = $this->actingAs($this->staffJuan)->post(route('staff.tasks.start', $task->id));
        $this->assertEquals('in_progress', $task->fresh()->status);

        // Juan submits proof
        $proofRes = $this->actingAs($this->staffJuan)->post(route('staff.tasks.submitProof', $task->id), [
            'completion_remarks' => 'Finished count and inspected temperature at -18C.',
        ]);
        $this->assertEquals('for_verification', $task->fresh()->status);

        // Other staff (Maria) tries to access Juan's task via URL -> gets 403 Forbidden
        $unauthorizedRes = $this->actingAs($this->staffMaria)->get(route('staff.tasks.show', $task->id));
        $unauthorizedRes->assertStatus(403);

        // Admin approves task
        $approveRes = $this->actingAs($this->admin)->post(route('admin.tasks.verify', $task->id), [
            'decision' => 'approve',
            'remarks' => 'Verified and approved.',
        ]);
        $this->assertEquals('completed', $task->fresh()->status);
    }

    public function test_admin_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Overview Dashboard');
        $response->assertSee('Total SKUs Active');
    }

    public function test_create_item_with_expiration_date_and_create_task_with_location_and_origin(): void
    {
        // 1. Create item with expiration date
        $itemRes = $this->actingAs($this->admin)->post(route('admin.inventory.store'), [
            'sku' => 'SKU-EXP-001',
            'name' => 'Fresh Milk Test',
            'uom' => 'cartons',
            'cost_price' => 75.00,
            'selling_price' => 110.00,
            'expiration_date' => now()->addMonths(3)->format('Y-m-d'),
            'min_threshold' => 10,
            'status' => 'active',
        ]);

        $itemRes->assertSessionHasNoErrors();
        $createdItem = Item::where('sku', 'SKU-EXP-001')->first();
        $this->assertNotNull($createdItem);
        $this->assertNotNull($createdItem->expiration_date);

        // 2. Create task with Mariveles barangay Location and Origin
        $taskRes = $this->actingAs($this->admin)->post(route('admin.tasks.store'), [
            'title' => 'Deliver dairy items to client',
            'assigned_to' => $this->staffJuan->id,
            'work_location' => 'Balon-Anito',
            'origin' => 'Biaan Warehouse Central',
            'priority' => 'high',
            'start_datetime' => now()->format('Y-m-d\TH:i'),
            'due_datetime' => now()->addHours(3)->format('Y-m-d\TH:i'),
        ]);

        $taskRes->assertSessionHasNoErrors();
        $createdTask = StaffTask::where('work_location', 'Balon-Anito')->first();
        $this->assertNotNull($createdTask);
        $this->assertEquals('Biaan Warehouse Central', $createdTask->origin);
        $this->assertEquals('Balon-Anito', $createdTask->work_location);
    }
}
