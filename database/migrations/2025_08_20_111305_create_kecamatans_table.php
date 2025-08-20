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
        Schema::create('kecamatans', function (Blueprint $table) {
            $table->string('kecamatan_code', 10)->primary(); // primary key
            $table->string('province_code', 10);
            $table->string('kabupaten_code', 10);
            $table->string('kecamatan_name');
            $table->timestamps();
            $table->foreign('province_code')
                  ->references('province_code')
                  ->on('provinces')
                  ->onDelete('cascade');

            $table->foreign('kabupaten_code')
                  ->references('kabupaten_code')
                  ->on('kabupaten')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kecamatans');
    }
};
