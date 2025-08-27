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
        Schema::create('link_kabupaten', function (Blueprint $table) {
            $table->string('province_code');
            $table->string('kabupaten_code');
            $table->string('link_no');
            $table->integer('drp_from');
            $table->integer('drp_to');
            $table->string('kabupaten')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('province_code')
                ->references('province_code')->on('provinces')
                ->onDelete('cascade');

            $table->foreign('kabupaten_code')
                ->references('kabupaten_code')->on('kabupaten')
                ->onDelete('cascade');

            $table->foreign('link_no')
                ->references('link_no')->on('link')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_kabupaten');
    }
};
