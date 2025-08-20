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
        Schema::create('island', function (Blueprint $table) {
            $table->string('island_code', 10)->primary();
            $table->string('province_code', 10);
            $table->string('island_name', 100);

            $table->timestamps();

            $table->foreign('province_code')
                  ->references('province_code')
                  ->on('provinces')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('island');
    }
};
