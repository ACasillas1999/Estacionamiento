<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->unsignedInteger('layout_x')->default(80)->after('is_active');
            $table->unsignedInteger('layout_y')->default(80)->after('layout_x');
            $table->unsignedInteger('layout_width')->default(82)->after('layout_y');
            $table->unsignedInteger('layout_height')->default(140)->after('layout_width');
            $table->smallInteger('layout_angle')->default(0)->after('layout_height');
        });

        $preset = [
            'A-01' => ['layout_x' => 255, 'layout_y' => 68],
            'A-02' => ['layout_x' => 355, 'layout_y' => 68],
            'A-03' => ['layout_x' => 455, 'layout_y' => 68],
            'A-04' => ['layout_x' => 555, 'layout_y' => 68],
            'B-01' => ['layout_x' => 390, 'layout_y' => 243],
            'B-02' => ['layout_x' => 490, 'layout_y' => 243],
            'B-03' => ['layout_x' => 590, 'layout_y' => 243],
            'B-04' => ['layout_x' => 690, 'layout_y' => 243],
            'C-01' => ['layout_x' => 245, 'layout_y' => 488],
            'C-02' => ['layout_x' => 345, 'layout_y' => 488],
            'M-01' => ['layout_x' => 72, 'layout_y' => 146, 'layout_width' => 70, 'layout_height' => 120, 'layout_angle' => 90],
            'M-02' => ['layout_x' => 72, 'layout_y' => 268, 'layout_width' => 70, 'layout_height' => 120, 'layout_angle' => 90],
        ];

        foreach ($preset as $code => $layout) {
            DB::table('parking_spots')->where('code', $code)->update(array_merge([
                'layout_width' => 82,
                'layout_height' => 140,
                'layout_angle' => 0,
            ], $layout));
        }
    }

    public function down(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->dropColumn([
                'layout_x',
                'layout_y',
                'layout_width',
                'layout_height',
                'layout_angle',
            ]);
        });
    }
};
