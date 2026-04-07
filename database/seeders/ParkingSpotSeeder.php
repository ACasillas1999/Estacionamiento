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
            ['code' => 'A-01', 'zone' => 'Norte', 'layout_x' => 255, 'layout_y' => 68, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'A-02', 'zone' => 'Norte', 'layout_x' => 355, 'layout_y' => 68, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'A-03', 'zone' => 'Norte', 'layout_x' => 455, 'layout_y' => 68, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'A-04', 'zone' => 'Norte', 'layout_x' => 555, 'layout_y' => 68, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'B-01', 'zone' => 'Centro', 'layout_x' => 390, 'layout_y' => 243, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'B-02', 'zone' => 'Centro', 'layout_x' => 490, 'layout_y' => 243, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'B-03', 'zone' => 'Centro', 'layout_x' => 590, 'layout_y' => 243, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'B-04', 'zone' => 'Centro', 'layout_x' => 690, 'layout_y' => 243, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'C-01', 'zone' => 'Sur', 'layout_x' => 245, 'layout_y' => 488, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'C-02', 'zone' => 'Sur', 'layout_x' => 345, 'layout_y' => 488, 'layout_width' => 82, 'layout_height' => 140, 'layout_angle' => 0],
            ['code' => 'M-01', 'zone' => 'Motos', 'layout_x' => 72, 'layout_y' => 146, 'layout_width' => 70, 'layout_height' => 120, 'layout_angle' => 90],
            ['code' => 'M-02', 'zone' => 'Motos', 'layout_x' => 72, 'layout_y' => 268, 'layout_width' => 70, 'layout_height' => 120, 'layout_angle' => 90],
        ];

        foreach ($spots as $spot) {
            ParkingSpot::query()->updateOrCreate(['code' => $spot['code']], $spot);
        }
    }
}
