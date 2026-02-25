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
        Schema::table('link_kecamatan', function (Blueprint $table) {
            // Drop foreign key & kolom lama
            $table->dropForeign(['link_id']);
            $table->dropColumn('link_id');

            // Tambah kolom baru
            $table->unsignedBigInteger('link_master_id')->after('kabupaten_code');
            $table->foreign('link_master_id')
                  ->references('id')
                  ->on('link_master')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('link_kecamatan', function (Blueprint $table) {
            $table->dropForeign(['link_master_id']);
            $table->dropColumn('link_master_id');

            $table->unsignedBigInteger('link_id')->after('kabupaten_code');
            $table->foreign('link_id')
                  ->references('id')
                  ->on('link')
                  ->onDelete('cascade');
        });
    }
};
