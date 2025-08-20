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
        Schema::create('link', function (Blueprint $table) {
            $table->string('link_no')->primary();
            $table->string('province_code');
            $table->string('kabupaten_code');

            $table->string('link_code')->nullable();
            $table->string('link_name')->nullable();
            $table->string('status')->nullable();
            $table->string('function')->nullable();
            $table->string('class')->nullable();
            $table->string('project_number')->nullable();
            $table->string('access_status')->nullable();

            $table->decimal('link_length_official', 10, 2)->nullable();
            $table->decimal('link_length_actual', 10, 2)->nullable();
            $table->decimal('WTI', 10, 2)->nullable();
            $table->decimal('MCA2', 10, 2)->nullable();
            $table->decimal('MCA3', 10, 2)->nullable();
            $table->decimal('MCA4', 10, 2)->nullable();
            $table->decimal('MCA5', 10, 2)->nullable();
            $table->decimal('CUMESA', 10, 2)->nullable();
            $table->decimal('ESA0', 10, 2)->nullable();
            $table->integer('AADT')->nullable();

            $table->timestamps();

            $table->foreign('province_code')
                ->references('province_code')
                ->on('provinces')
                ->onDelete('cascade');

            $table->foreign('kabupaten_code')
                ->references('kabupaten_code')
                ->on('kabupaten')
                ->onDelete('cascade');
            
            $table->foreign('status')
                ->references('code')
                ->on('code_link_status')
                ->onDelete('cascade');

            $table->foreign('function')
                ->references('code')
                ->on('code_link_function')
                ->onDelete('cascade');

            $table->foreign('class')
                ->references('code')
                ->on('code_link_class')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link');
    }
};
