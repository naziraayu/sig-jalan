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
        Schema::create('roughness', function (Blueprint $table) {
            $table->integer('year');
            $table->string('province_code', 50);
            $table->string('kabupaten_code', 50);
            $table->unsignedBigInteger('link_id', 50);
            $table->decimal('chainage_from', 10, 2)->nullable(); // Number (calculated)
            $table->decimal('chainage_to', 10, 2)->nullable();   // Number (calculated)
            $table->decimal('drp_from', 10, 2)->nullable();
            $table->decimal('offset_from', 10, 2)->nullable();
            $table->decimal('drp_to', 10, 2)->nullable();
            $table->decimal('offset_to', 10, 2)->nullable();
            $table->decimal('iri', 10, 2)->nullable();           // m2
            $table->boolean('analysis_base_year')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roughness');
    }
};
