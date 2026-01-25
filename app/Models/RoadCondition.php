<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoadCondition extends Model
{
    // use HasFactory;
    protected $table = 'road_condition';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = [
        'year',
        'sdi_value',
        'sdi_category',
        'reference_year',
        'province_code',
        'kabupaten_code',
        'link_id',
        'link_no',
        'chainage_from',
        'chainage_to',
        'drp_from',
        'offset_from',
        'drp_to',
        'offset_to',
        'roughness',
        'bleeding_area',
        'ravelling_area',
        'desintegration_area',
        'crack_dep_area',
        'patching_area',
        'oth_crack_area',
        'pothole_area',
        'rutting_area',
        'edge_damage_area',
        'crossfall_area',
        'depressions_area',
        'erosion_area',
        'waviness_area',
        'gravel_thickness_area',
        'concrete_cracking_area',
        'concrete_spalling_area',
        'concrete_structural_cracking_area',
        'concrete_corner_break_no',
        'concrete_pumping_no',
        'concrete_blowouts_area',
        'crack_width',
        'pothole_count',
        'rutting_depth',
        'shoulder_l',
        'shoulder_r',
        'drain_l',
        'drain_r',
        'slope_l',
        'slope_r',
        'footpath_l',
        'footpath_r',
        'sign_l',
        'sign_r',
        'guide_post_l',
        'guide_post_r',
        'barrier_l',
        'barrier_r',
        'road_marking_l',
        'road_marking_r',
        'iri',
        'rci',
        'analysis_base_year',
        'segment_tti',
        'survey_by',
        'paved',
        'pavement',
        'check_data',
        'composition',
        'crack_type',
        'pothole_size',
        'should_cond_l',
        'should_cond_r',
        'crossfall_shape',
        'gravel_size',
        'gravel_thickness',
        'distribution',
        'edge_damage_area_r',
        'survey_by2',
        'survey_date',
        'section_status',
    ];

     protected $casts = [
        'year' => 'integer',
        'reference_year' => 'integer',
        'roughness' => 'boolean',
        'paved' => 'boolean',
        'chainage_from' => 'decimal:2',
        'chainage_to' => 'decimal:2',
    ];

    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'Province_Code', 'province_code');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'Kabupaten_Code', 'kabupaten_code');
    }

    public function linkNo()
    {
        return $this->belongsTo(Link::class, 'link_no', 'link_no')
                    ->where('year', $this->reference_year);
    }

    // Relasi ke LinkMaster via Link
    public function master()
    {
        return $this->hasOneThrough(
            LinkMaster::class,
            Link::class,
            'link_no',
            'id',
            'link_no',
            'master_link_id'
        )->where('link.year', $this->reference_year);
    }

    public function inventory()
    {
        return $this->belongsTo(RoadInventory::class, 'link_no', 'link_no')
                    ->when($this->chainage_from !== null, function($query) {
                        return $query->where('chainage_from', '<=', $this->chainage_from);
                    })
                    ->when($this->chainage_to !== null, function($query) {
                        return $query->where('chainage_to', '>=', $this->chainage_to);
                    });
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByReferenceYear($query, $year)
    {
        return $query->where('reference_year', $year);
    }

    // Scope: With link and master
    public function scopeWithRelations($query)
    {
        return $query->with(['link.master']);
    }

    // contoh relasi ke tabel code conditions
    public function Lshoulder()
    {
        return $this->hasMany(CodeShoulderCondition::class, 'code', 'shoulder_l');
    }
    public function Rshoulder()
    {
        return $this->hasMany(CodeShoulderCondition::class, 'code', 'shoulder_r');
    }

    public function Ldrain()
    {
        return $this->hasMany(CodeDrainCondition::class, 'code', 'drain_l');
    }
    public function Rdrain()
    {
        return $this->hasMany(CodeDrainCondition::class, 'code', 'drain_r');
    }

    public function Lslope()
    {
        return $this->hasMany(CodeSlopeCondition::class, 'code', 'slope_l');
    }
    public function Rslope()
    {
        return $this->hasMany(CodeSlopeCondition::class, 'code', 'slope_r');
    }

    public function Lfootpath()
    {
        return $this->hasMany(CodeFoothpathCondition::class, 'code', 'footpath_l');
    }
    public function Rfootpath()
    {
        return $this->hasMany(CodeFoothpathCondition::class, 'code', 'footpath_r');
    }

}
