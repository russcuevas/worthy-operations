<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SupervisorPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_logs_in_and_is_redirected_to_supervisor_dashboard()
    {
        $supervisor = User::factory()->create([
            'username' => 'supervisor_test',
            'role' => 'supervisor',
            'password' => Hash::make('secret123'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'login' => 'supervisor_test',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/supervisor/dashboard');
    }

    public function test_supervisor_can_access_supervisor_routes()
    {
        $supervisor = User::factory()->create([
            'username' => 'supervisor_test2',
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        $response = $this->actingAs($supervisor)->get('/supervisor/dashboard');
        $response->assertStatus(200);
        $response->assertSee('SUPERVISOR PORTAL');

        $response = $this->actingAs($supervisor)->get('/supervisor/inventory');
        $response->assertStatus(200);

        $response = $this->actingAs($supervisor)->get('/supervisor/tasks');
        $response->assertStatus(200);
    }

    public function test_supervisor_is_redirected_away_from_admin_routes()
    {
        $supervisor = User::factory()->create([
            'username' => 'supervisor_test3',
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        $response = $this->actingAs($supervisor)->get('/admin/dashboard');
        $response->assertRedirect('/supervisor/dashboard');
    }

    public function test_supervisor_can_access_calendar_and_fetch_events()
    {
        $supervisor = User::factory()->create([
            'username' => 'supervisor_cal',
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        $response = $this->actingAs($supervisor)->get('/supervisor/calendar');
        $response->assertStatus(200);
        $response->assertSee('FullCalendar');

        $eventsResponse = $this->actingAs($supervisor)->get('/supervisor/calendar/events?assigned_to=all');
        $eventsResponse->assertStatus(200);
        $eventsResponse->assertJson([]);
    }

    public function test_supervisor_can_update_warehouse_and_storage_location()
    {
        $supervisor = User::factory()->create([
            'username' => 'supervisor_wh',
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        $warehouse = \App\Models\Warehouse::create([
            'name' => 'Original WH',
            'code' => 'WH-ORIG',
            'status' => 'active',
        ]);

        $response = $this->actingAs($supervisor)->putJson("/supervisor/warehouses/{$warehouse->id}", [
            'name' => 'Updated WH',
            'code' => 'WH-UPD',
            'status' => 'active',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'name' => 'Updated WH',
            'code' => 'WH-UPD',
        ]);
    }
}
