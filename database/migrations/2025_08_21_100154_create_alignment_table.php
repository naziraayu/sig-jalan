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
        Schema::create('alignment', function (Blueprint $table) {
            $table->integer('chainage')->index(); 
            $table->string('province_code');
            $table->string('kabupaten_code');
            $table->unsignedBigInteger('link_master_id'); // relasi ke tabel link
            $table->string('link_no');
            $table->integer('year');

            $table->integer('chainage_rb')->nullable();
            $table->integer('gpspoint_north_deg')->nullable();
            $table->integer('gpspoint_north_min')->nullable();
            $table->integer('gpspoint_north_sec')->nullable();
            $table->integer('gpspoint_east_deg')->nullable();
            $table->integer('gpspoint_east_min')->nullable();
            $table->integer('gpspoint_east_sec')->nullable();
            $table->string('section_wkt_linestring')->nullable();
            $table->double('east')->nullable();
            $table->double('north')->nullable();
            $table->timestamps();

             $table->foreign('province_code')
                ->references('province_code')->on('provinces')
                ->onDelete('cascade');

            $table->foreign('kabupaten_code')
                ->references('kabupaten_code')->on('kabupaten')
                ->onDelete('cascade');

            $table->foreign('link_master_id')
                ->references('id')->on('link_master')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alignment');
    }
};
