<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingLayout extends Model
{
    protected $fillable = [
        'name',
        'canvas_width',
        'canvas_height',
        'show_grid',
        'decorations',
    ];

    protected $casts = [
        'show_grid' => 'boolean',
        'decorations' => 'array',
    ];

    public static function primary(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Plano principal',
                'canvas_width' => 1120,
                'canvas_height' => 720,
                'show_grid' => true,
                'decorations' => static::defaultDecorations(),
            ],
        );
    }

    public static function defaultDecorations(): array
    {
        return [
            ['type' => 'lane', 'label' => 'Bahia norte', 'x' => 150, 'y' => 70, 'width' => 760, 'height' => 120, 'rotation' => 0],
            ['type' => 'island', 'label' => 'Isla central', 'x' => 350, 'y' => 255, 'width' => 330, 'height' => 115, 'rotation' => 0],
            ['type' => 'lane', 'label' => 'Bahia sur', 'x' => 155, 'y' => 470, 'width' => 630, 'height' => 120, 'rotation' => 0],
            ['type' => 'building', 'label' => 'Caseta / bodega', 'x' => 860, 'y' => 255, 'width' => 160, 'height' => 255, 'rotation' => 0],
            ['type' => 'entry', 'label' => 'Acceso', 'x' => 760, 'y' => 565, 'width' => 185, 'height' => 55, 'rotation' => 0],
            ['type' => 'label', 'label' => 'Calle principal', 'x' => 415, 'y' => 650, 'width' => 240, 'height' => 28, 'rotation' => 0],
            ['type' => 'label', 'label' => 'Circulacion', 'x' => 475, 'y' => 205, 'width' => 160, 'height' => 28, 'rotation' => 0],
        ];
    }
}
