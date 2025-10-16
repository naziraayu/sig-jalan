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
        Schema::create('road_inventory', function (Blueprint $table) {
            $table->string('province_code');
            $table->string('kabupaten_code');
            $table->unsignedBigInteger('link_id');
            $table->integer('year')->nullable();
            $table->integer('chainage_from')->index();
            $table->integer('chainage_to');
            $table->integer('drp_from')->nullable();
            $table->integer('offset_from')->nullable();
            $table->integer('drp_to')->nullable();
            $table->integer('offset_to')->nullable();
            $table->integer('pave_width')->nullable();
            $table->integer('row')->nullable();
            $table->string('pave_type')->index()->nullable();
            $table->integer('should_width_L')->nullable();
            $table->integer('should_width_R')->nullable();
            $table->string('should_type_L')->index()->nullable();
            $table->string('should_type_R')->index()->nullable();
            $table->string('drain_type_L')->index()->nullable();
            $table->string('drain_type_R')->index()->nullable();
            $table->string('terrain')->index()->nullable();
            $table->string('land_use_L')->index()->nullable();
            $table->string('land_use_R')->index()->nullable();
            $table->boolean('impassable')->default(false);
            $table->string('impassable_reason')->index()->nullable();
            $table->timestamps();

            $table->foreign('province_code')
                ->references('province_code')->on('provinces')
                ->onDelete('cascade');

            $table->foreign('kabupaten_code')
                ->references('kabupaten_code')->on('kabupaten')
                ->onDelete('cascade');

            $table->foreign('link_id')
                ->references('id')->on('link')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('road_inventory');
    }
};
