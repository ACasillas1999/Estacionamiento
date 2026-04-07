<?php

use App\Models\ParkingLayout;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->default('Plano principal');
            $table->unsignedInteger('canvas_width')->default(1120);
            $table->unsignedInteger('canvas_height')->default(720);
            $table->boolean('show_grid')->default(true);
            $table->json('decorations')->nullable();
            $table->timestamps();
        });

        ParkingLayout::query()->create([
            'name' => 'Plano principal',
            'canvas_width' => 1120,
            'canvas_height' => 720,
            'show_grid' => true,
            'decorations' => ParkingLayout::defaultDecorations(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_layouts');
    }
};
