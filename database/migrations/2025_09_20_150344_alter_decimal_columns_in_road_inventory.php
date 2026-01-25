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
        Schema::table('road_inventory', function (Blueprint $table) {
            $table->decimal('drp_from', 10, 2)->nullable()->change();
            $table->decimal('offset_from', 10, 2)->nullable()->change();
            $table->decimal('drp_to', 10, 2)->nullable()->change();
            $table->decimal('offset_to', 10, 2)->nullable()->change();
            $table->decimal('pave_width', 8, 2)->nullable()->change();
            $table->decimal('row', 8, 2)->nullable()->change();
            $table->decimal('should_width_L', 8, 2)->nullable()->change();
            $table->decimal('should_width_R', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('road_inventory', function (Blueprint $table) {
            //
        });
    }
};
