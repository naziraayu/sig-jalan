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
        Schema::create('road_condition', function (Blueprint $table) {
            // Basic information
            $table->integer('year');
            $table->string('province_code', 10);
            $table->string('kabupaten_code', 10);
            $table->unsignedBigInteger('link_id', 50);
            
            // Chainage information
            $table->decimal('chainage_from', 10, 2)->nullable();
            $table->decimal('chainage_to', 10, 2)->nullable();
            $table->decimal('drp_from', 10, 2)->nullable();
            $table->decimal('offset_from', 10, 2)->nullable();
            $table->decimal('drp_to', 10, 2)->nullable();
            $table->decimal('offset_to', 10, 2)->nullable();
            
            // Road condition measurements (m2)
            $table->boolean('roughness')->default(false)->nullable();
            $table->decimal('bleeding_area', 10, 2)->default(0)->nullable();
            $table->decimal('ravelling_area', 10, 2)->default(0)->nullable();
            $table->decimal('desintegration_area', 10, 2)->default(0)->nullable();
            $table->decimal('crack_dep_area', 10, 2)->default(0)->nullable();
            $table->decimal('patching_area', 10, 2)->default(0)->nullable();
            $table->decimal('oth_crack_area', 10, 2)->default(0)->nullable();
            $table->decimal('pothole_area', 10, 2)->default(0)->nullable();
            $table->decimal('rutting_area', 10, 2)->default(0)->nullable();
            $table->decimal('edge_damage_area', 10, 2)->default(0)->nullable();
            $table->decimal('crossfall_area', 10, 2)->default(0)->nullable();
            $table->decimal('depressions_area', 10, 2)->default(0)->nullable();
            $table->decimal('erosion_area', 10, 2)->default(0)->nullable();
            $table->decimal('waviness_area', 10, 2)->default(0)->nullable();
            $table->decimal('gravel_thickness_area', 10, 2)->default(0)->nullable();
            
            // Concrete specific measurements
            $table->decimal('concrete_cracking_area', 10, 2)->nullable();
            $table->decimal('concrete_spalling_area', 10, 2)->nullable();
            $table->decimal('concrete_structural_cracking_area', 10, 2)->nullable();
            $table->integer('concrete_corner_break_no')->default(0)->nullable();
            $table->integer('concrete_pumping_no')->default(0)->nullable();
            $table->decimal('concrete_blowouts_area', 10, 2)->default(0)->nullable();
            
            // Additional measurements
            $table->string('crack_width', 10)->nullable();
            $table->integer('pothole_count')->default(0)->nullable();
            $table->string('rutting_depth', 10)->nullable();
            
            // Shoulder conditions
            $table->string('shoulder_l', 50)->nullable();
            $table->string('shoulder_r', 50)->nullable();
            
            // Infrastructure conditions
            $table->string('drain_l', 50)->nullable();
            $table->string('drain_r', 50)->nullable();
            $table->string('slope_l', 50)->nullable();
            $table->string('slope_r', 50)->nullable();
            $table->string('footpath_l', 50)->nullable();
            $table->string('footpath_r', 50)->nullable();
            
            // Signs and barriers (measurements in meters)
            $table->decimal('sign_l', 10, 2)->default(0)->nullable();
            $table->decimal('sign_r', 10, 2)->default(0)->nullable();
            $table->decimal('guide_post_l', 10, 2)->default(0)->nullable();
            $table->decimal('guide_post_r', 10, 2)->default(0)->nullable();
            $table->decimal('barrier_l', 10, 2)->default(0)->nullable();
            $table->decimal('barrier_r', 10, 2)->default(0)->nullable();
            
            // Road marking
            $table->boolean('road_marking_l')->default(false)->nullable();
            $table->boolean('road_marking_r')->default(false)->nullable();
            
            // Road quality indices
            $table->decimal('iri', 8, 2)->nullable();
            $table->decimal('rci', 8, 2)->nullable();
            $table->boolean('analysis_base_year')->default(false)->nullable();
            $table->decimal('segment_tti', 8, 2)->nullable();
            
            // Survey information
            $table->string('survey_by', 100)->nullable();
            $table->boolean('paved')->default(true)->nullable();
            $table->string('pavement', 50)->nullable();
            $table->boolean('check_data')->default(false)->nullable();
            $table->decimal('composition', 8, 2)->nullable();
            $table->decimal('crack_type', 8, 2)->nullable();
            
            // Additional condition details
            $table->decimal('pothole_size', 8, 2)->nullable();
            $table->string('should_cond_l', 50)->nullable();
            $table->string('should_cond_r', 50)->nullable();
            $table->integer('crossfall_shape')->nullable();
            $table->decimal('gravel_size', 8, 2)->nullable();
            $table->decimal('gravel_thickness', 8, 2)->nullable();
            $table->integer('distribution')->nullable();
            $table->decimal('edge_damage_area_r', 10, 2)->default(0)->nullable();
            $table->string('survey_by2', 100)->nullable();
            $table->timestamp('survey_date')->nullable();
            $table->decimal('section_status', 8, 2)->default(0)->nullable();
            
            $table->timestamps();

            $table->foreign('province_code')
                ->references('province_code')->on('provinces')
                ->onDelete('cascade');

            $table->foreign('kabupaten_code')
                ->references('kabupaten_code')->on('kabupaten')
                ->onDelete('cascade');

            $table->foreign('link_id')
                ->references('id')->on('link')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('road_condition');
    }
};
