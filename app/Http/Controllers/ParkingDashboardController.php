<?php

namespace App\Http\Controllers;

use App\Models\ParkingLayout;
use App\Models\ParkingSession;
use App\Models\ParkingSpot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParkingDashboardController extends Controller
{
    public function index(): View
    {
        $layout = ParkingLayout::primary();
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
            'layout' => $layout,
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

    public function layoutEditor(): View
    {
        return view('parking.layout-editor', [
            'layout' => ParkingLayout::primary(),
            'spots' => ParkingSpot::query()->orderBy('code')->get(),
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
            ...$this->defaultSpotLayout(ParkingSpot::query()->count()),
        ]);

        return to_route('dashboard')->with('status', 'Cajon creado correctamente.');
    }

    public function updateLayout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'canvas_width' => ['required', 'integer', 'min:700', 'max:1800'],
            'canvas_height' => ['required', 'integer', 'min:500', 'max:1400'],
            'show_grid' => ['nullable', 'boolean'],
            'spots' => ['required', 'array', 'min:1'],
            'spots.*.id' => ['required', 'exists:parking_spots,id'],
            'spots.*.layout_x' => ['required', 'integer', 'min:0', 'max:2000'],
            'spots.*.layout_y' => ['required', 'integer', 'min:0', 'max:2000'],
            'spots.*.layout_width' => ['required', 'integer', 'min:50', 'max:220'],
            'spots.*.layout_height' => ['required', 'integer', 'min:80', 'max:280'],
            'spots.*.layout_angle' => ['required', 'integer', 'min:-180', 'max:180'],
            'decorations' => ['nullable', 'array'],
            'decorations.*.type' => ['nullable', 'in:lane,island,building,label,entry'],
            'decorations.*.label' => ['nullable', 'string', 'max:80'],
            'decorations.*.x' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'decorations.*.y' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'decorations.*.width' => ['nullable', 'integer', 'min:10', 'max:2000'],
            'decorations.*.height' => ['nullable', 'integer', 'min:10', 'max:2000'],
            'decorations.*.rotation' => ['nullable', 'integer', 'min:-180', 'max:180'],
        ]);

        $layout = ParkingLayout::primary();
        $decorations = collect($validated['decorations'] ?? [])
            ->map(function (array $element) {
                $type = $element['type'] ?? null;

                if ($type === null || $type === '') {
                    return null;
                }

                return [
                    'type' => $type,
                    'label' => trim((string) ($element['label'] ?? '')),
                    'x' => (int) ($element['x'] ?? 0),
                    'y' => (int) ($element['y'] ?? 0),
                    'width' => (int) ($element['width'] ?? 10),
                    'height' => (int) ($element['height'] ?? 10),
                    'rotation' => (int) ($element['rotation'] ?? 0),
                ];
            })
            ->filter()
            ->values()
            ->all();

        $layout->update([
            'name' => $validated['name'],
            'canvas_width' => $validated['canvas_width'],
            'canvas_height' => $validated['canvas_height'],
            'show_grid' => $request->boolean('show_grid'),
            'decorations' => $decorations,
        ]);

        foreach ($validated['spots'] as $spotData) {
            ParkingSpot::query()->whereKey($spotData['id'])->update([
                'layout_x' => $spotData['layout_x'],
                'layout_y' => $spotData['layout_y'],
                'layout_width' => $spotData['layout_width'],
                'layout_height' => $spotData['layout_height'],
                'layout_angle' => $spotData['layout_angle'],
            ]);
        }

        return to_route('layout.editor')->with('status', 'Plano actualizado correctamente.');
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

    private function defaultSpotLayout(int $index): array
    {
        $column = $index % 5;
        $row = intdiv($index, 5);

        return [
            'layout_x' => 120 + ($column * 105),
            'layout_y' => 90 + ($row * 170),
            'layout_width' => 82,
            'layout_height' => 140,
            'layout_angle' => 0,
        ];
    }
}
