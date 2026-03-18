<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingSession extends Model
{
    protected $fillable = [
        'parking_spot_id',
        'plate_number',
        'driver_name',
        'vehicle_type',
        'entry_time',
        'exit_time',
        'hourly_rate',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function parkingSpot(): BelongsTo
    {
        return $this->belongsTo(ParkingSpot::class);
    }

    public function isOpen(): bool
    {
        return $this->exit_time === null;
    }

    public function close(CarbonInterface $exitTime): void
    {
        $minutes = max(1, $this->entry_time->diffInMinutes($exitTime));
        $hours = (int) ceil($minutes / 60);

        $this->forceFill([
            'exit_time' => $exitTime,
            'total_amount' => $hours * (float) $this->hourly_rate,
        ])->save();
    }
}
