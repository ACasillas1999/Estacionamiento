<?php

namespace App\Http\Controllers;

use App\Models\ParkingSession;
use App\Models\ParkingSpot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParkingDashboardController extends Controller
{
    public function index(): View
    {
        $spots = ParkingSpot::query()
            ->with('activeSession')
            ->orderBy('code')
            ->get();

        $availableSpots = $spots
            ->where('is_active', true)
            ->filter(fn (ParkingSpot $spot) => $spot->activeSession === null);

        $occupiedSpots = $spots
            ->where('is_active', true)
            ->filter(fn (ParkingSpot $spot) => $spot->activeSession !== null);

        return view('parking.dashboard', [
            'spots' => $spots,
            'availableSpots' => $availableSpots,
            'occupiedSpots' => $occupiedSpots,
            'stats' => [
                'total' => $spots->where('is_active', true)->count(),
                'available' => $availableSpots->count(),
                'occupied' => $occupiedSpots->count(),
                'inactive' => $spots->where('is_active', false)->count(),
            ],
        ]);
    }

    public function history(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $sessions = ParkingSession::query()
            ->with('parkingSpot')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('plate_number', 'like', "%{$search}%")
                        ->orWhere('driver_name', 'like', "%{$search}%")
                        ->orWhere('vehicle_type', 'like', "%{$search}%")
                        ->orWhereHas('parkingSpot', function ($spotQuery) use ($search) {
                            $spotQuery->where('code', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('entry_time')
            ->paginate(15)
            ->withQueryString();

        return view('parking.history', [
            'sessions' => $sessions,
            'search' => $search,
        ]);
    }

    public function storeSpot(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('spot', [
            'code' => ['required', 'string', 'max:20', 'unique:parking_spots,code'],
            'zone' => ['nullable', 'string', 'max:30'],
        ]);

        ParkingSpot::create([
            'code' => strtoupper($validated['code']),
            'zone' => $validated['zone'] ?? null,
            'is_active' => true,
        ]);

        return to_route('dashboard')->with('status', 'Cajon creado correctamente.');
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('checkIn', [
            'parking_spot_id' => ['required', 'exists:parking_spots,id'],
            'plate_number' => ['required', 'string', 'max:15'],
            'driver_name' => ['nullable', 'string', 'max:80'],
            'vehicle_type' => ['required', 'in:Auto,Moto,Camioneta'],
            'hourly_rate' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:300'],
        ]);

        $spot = ParkingSpot::query()->with('activeSession')->findOrFail($validated['parking_spot_id']);

        if (! $spot->is_active) {
            return back()->withErrors([
                'parking_spot_id' => 'El cajon seleccionado esta fuera de servicio.',
            ], 'checkIn')->withInput();
        }

        if ($spot->activeSession !== null) {
            return back()->withErrors([
                'parking_spot_id' => 'El cajon seleccionado ya esta ocupado.',
            ], 'checkIn')->withInput();
        }

        ParkingSession::create([
            'parking_spot_id' => $spot->id,
            'plate_number' => strtoupper($validated['plate_number']),
            'driver_name' => $validated['driver_name'] ?? null,
            'vehicle_type' => $validated['vehicle_type'],
            'entry_time' => now(),
            'hourly_rate' => $validated['hourly_rate'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return to_route('dashboard')->with('status', 'Entrada registrada correctamente.');
    }

    public function checkOut(ParkingSession $parkingSession): RedirectResponse
    {
        if (! $parkingSession->isOpen()) {
            return to_route('dashboard')->with('status', 'La salida ya habia sido registrada.');
        }

        $parkingSession->close(now());

        return to_route('dashboard')->with('status', 'Salida registrada correctamente.');
    }
}
