<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ParkingSpot extends Model
{
    protected $fillable = [
        'code',
        'zone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(ParkingSession::class);
    }

    public function activeSession(): HasOne
    {
        return $this->hasOne(ParkingSession::class)->whereNull('exit_time');
    }

    public static function availableList(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->whereDoesntHave('activeSession')
            ->orderBy('code')
            ->get();
    }
}
