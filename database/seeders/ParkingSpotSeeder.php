<?php

namespace Database\Seeders;

use App\Models\ParkingSpot;
use Illuminate\Database\Seeder;

class ParkingSpotSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $spots = [
            ['code' => 'A-01', 'zone' => 'Norte'],
            ['code' => 'A-02', 'zone' => 'Norte'],
            ['code' => 'A-03', 'zone' => 'Norte'],
            ['code' => 'A-04', 'zone' => 'Norte'],
            ['code' => 'B-01', 'zone' => 'Centro'],
            ['code' => 'B-02', 'zone' => 'Centro'],
            ['code' => 'B-03', 'zone' => 'Centro'],
            ['code' => 'B-04', 'zone' => 'Centro'],
            ['code' => 'C-01', 'zone' => 'Sur'],
            ['code' => 'C-02', 'zone' => 'Sur'],
            ['code' => 'M-01', 'zone' => 'Motos'],
            ['code' => 'M-02', 'zone' => 'Motos'],
        ];

        foreach ($spots as $spot) {
            ParkingSpot::query()->firstOrCreate(['code' => $spot['code']], $spot);
        }
    }
}
