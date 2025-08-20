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
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->string('kabupaten_code', 10)->primary();
            $table->string('province_code', 10);
            $table->string('kabupaten_name', 100);
            $table->string('balai_code', 10)->nullable();
            $table->string('island_code', 10)->nullable();
            $table->boolean('default_kabupaten')->default(false);
            $table->integer('stable')->nullable();

            $table->timestamps();

            // Relasi ke provinces
            $table->foreign('province_code')
                  ->references('province_code')
                  ->on('provinces')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Relasi ke balai
            $table->foreign('balai_code')
                  ->references('balai_code')
                  ->on('balai')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            // Relasi ke island (opsional)
            $table->foreign('island_code')
                  ->references('island_code')
                  ->on('island')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupaten');
    }
};
