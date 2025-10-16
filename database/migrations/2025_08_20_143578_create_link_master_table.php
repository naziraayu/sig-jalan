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
        Schema::create('link_master', function (Blueprint $table) {
            $table->id();
            $table->string('link_no')->nullable();
            $table->string('link_name')->nullable();
            $table->integer('province_code')->nullable();
            $table->integer('kabupaten_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_master');
    }
};
