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

    public function billedMinutes(?CarbonInterface $endTime = null): int
    {
        $endTime ??= $this->exit_time ?? now();

        return max(1, $this->entry_time->diffInMinutes($endTime));
    }

    public function billedHours(?CarbonInterface $endTime = null): int
    {
        return (int) ceil($this->billedMinutes($endTime) / 60);
    }

    public function currentAmount(?CarbonInterface $endTime = null): float
    {
        return $this->billedHours($endTime) * (float) $this->hourly_rate;
    }

    public function close(CarbonInterface $exitTime): void
    {
        $this->forceFill([
            'exit_time' => $exitTime,
            'total_amount' => $this->currentAmount($exitTime),
        ])->save();
    }
}
