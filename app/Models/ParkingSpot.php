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
        'layout_x',
        'layout_y',
        'layout_width',
        'layout_height',
        'layout_angle',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'layout_x' => 'integer',
        'layout_y' => 'integer',
        'layout_width' => 'integer',
        'layout_height' => 'integer',
        'layout_angle' => 'integer',
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
