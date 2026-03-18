<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_spot_id')->constrained()->cascadeOnDelete();
            $table->string('plate_number', 15);
            $table->string('driver_name', 80)->nullable();
            $table->string('vehicle_type', 20);
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->decimal('hourly_rate', 8, 2)->default(25);
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_sessions');
    }
};
