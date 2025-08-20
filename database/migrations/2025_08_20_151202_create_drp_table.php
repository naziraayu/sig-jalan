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
        Schema::create('drp', function (Blueprint $table) {
            $table->string('province_code');
            $table->string('kabupaten_code');
            $table->string('link_no');

            $table->integer('drp_num')->unique();
            $table->decimal('chainage', 10, 2)->nullable();
            $table->integer('drp_order')->nullable();
            $table->decimal('drp_length', 10, 2)->nullable();

            $table->integer('dpr_north_deg')->nullable();
            $table->integer('dpr_north_min')->nullable();
            $table->decimal('dpr_north_sec', 10, 2)->nullable();

            $table->integer('dpr_east_deg')->nullable();
            $table->integer('dpr_east_min')->nullable();
            $table->decimal('dpr_east_sec', 10, 2)->nullable();

            $table->string('drp_type')->nullable();
            $table->string('drp_desc')->nullable();
            $table->string('drp_comment')->nullable();

            $table->timestamps();

            // Relasi ke provinsi & kabupaten
            $table->foreign('province_code')
                ->references('province_code')
                ->on('provinces')
                ->onDelete('cascade');

            $table->foreign('kabupaten_code')
                ->references('kabupaten_code')
                ->on('kabupaten')
                ->onDelete('cascade');

            // Relasi ke code_drptype
            $table->foreign('drp_type')
                ->references('code')
                ->on('code_drptype')
                ->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drp');
    }
};
