<?php

namespace Tests\Feature;

use App\Models\ParkingSession;
use App\Models\ParkingSpot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_spots(): void
    {
        $spot = ParkingSpot::query()->create([
            'code' => 'A-01',
            'zone' => 'Norte',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Estacionamiento A');
        $response->assertSee($spot->code);
    }

    public function test_history_module_loads_sessions(): void
    {
        $spot = ParkingSpot::query()->create([
            'code' => 'C-01',
            'zone' => 'Sur',
            'is_active' => true,
        ]);

        ParkingSession::query()->create([
            'parking_spot_id' => $spot->id,
            'plate_number' => 'HIS123',
            'driver_name' => 'Maria',
            'vehicle_type' => 'Auto',
            'entry_time' => now()->subHour(),
            'exit_time' => now(),
            'hourly_rate' => 25,
            'total_amount' => 25,
        ]);

        $response = $this->get(route('history.index'));

        $response->assertOk();
        $response->assertSee('Historial de Movimientos');
        $response->assertSee('HIS123');
    }

    public function test_vehicle_can_check_in_and_out(): void
    {
        $spot = ParkingSpot::query()->create([
            'code' => 'B-01',
            'zone' => 'Centro',
            'is_active' => true,
        ]);

        $this->post(route('sessions.check-in'), [
            'parking_spot_id' => $spot->id,
            'plate_number' => 'XYZ123',
            'driver_name' => 'Alex',
            'vehicle_type' => 'Auto',
            'hourly_rate' => 30,
            'notes' => 'Prueba',
        ])->assertRedirect(route('dashboard'));

        $session = ParkingSession::query()->first();

        $this->assertNotNull($session);
        $this->assertNull($session->exit_time);
        $this->assertSame('XYZ123', $session->plate_number);

        $this->patch(route('sessions.check-out', $session))
            ->assertRedirect(route('dashboard'));

        $session->refresh();

        $this->assertNotNull($session->exit_time);
        $this->assertNotNull($session->total_amount);
    }

    public function test_check_in_success_message_is_rendered_as_toast(): void
    {
        $spot = ParkingSpot::query()->create([
            'code' => 'D-01',
            'zone' => 'Centro',
            'is_active' => true,
        ]);

        $response = $this->followingRedirects()->post(route('sessions.check-in'), [
            'parking_spot_id' => $spot->id,
            'plate_number' => 'POP123',
            'driver_name' => 'Luis',
            'vehicle_type' => 'Auto',
            'hourly_rate' => 25,
        ]);

        $response->assertOk();
        $response->assertSee('Entrada registrada correctamente.');
        $response->assertSee('data-toast', false);
        $response->assertDontSee('<div class="alert success">', false);
    }

    public function test_dashboard_shows_live_elapsed_time_and_current_amount_for_open_sessions(): void
    {
        $spot = ParkingSpot::query()->create([
            'code' => 'A-01',
            'zone' => 'Norte',
            'is_active' => true,
        ]);

        ParkingSession::query()->create([
            'parking_spot_id' => $spot->id,
            'plate_number' => 'ABC123',
            'driver_name' => 'Maria',
            'vehicle_type' => 'Auto',
            'entry_time' => now()->subMinutes(47),
            'hourly_rate' => 30,
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('ABC123');
        $response->assertSee('00:47:00', false);
        $response->assertSee('$30.00', false);
    }
}
