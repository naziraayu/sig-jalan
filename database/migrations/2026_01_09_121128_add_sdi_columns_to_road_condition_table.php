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
        Schema::table('road_condition', function (Blueprint $table) {
            $table->decimal('sdi_value', 8, 2)->nullable()->after('year');
            $table->string('sdi_category', 50)->nullable()->after('sdi_value');
            $table->index('sdi_value'); // Index untuk query cepat
            $table->index('sdi_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('road_condition', function (Blueprint $table) {
            $table->dropIndex(['sdi_value']);
            $table->dropIndex(['sdi_category']);
            $table->dropColumn(['sdi_value', 'sdi_category']);
        });
    }
};
