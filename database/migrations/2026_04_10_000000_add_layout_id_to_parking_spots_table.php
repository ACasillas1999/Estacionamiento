<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->foreignId('parking_layout_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Assign existing spots to the first layout
        $firstLayoutId = DB::table('parking_layouts')->value('id');
        if ($firstLayoutId) {
            DB::table('parking_spots')->update(['parking_layout_id' => $firstLayoutId]);
        }
        
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->foreignId('parking_layout_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->dropForeign(['parking_layout_id']);
            $table->dropColumn('parking_layout_id');
        });
    }
};
