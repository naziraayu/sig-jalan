<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadInventory extends Model
{
    protected $table = "road_inventory";
    protected $primaryKey = 'nul';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'province_code',
        'kabupaten_code',
        'link_no',
        'chainage_from',
        'chainage_to',
        'drp_from',
        'offset_from',
        'drp_to',
        'offset_to',
        'pave_width',
        'row',
        'pave_type',
        'should_with_L',
        'should_with_R',
        'should_type_L',
        'should_type_R',
        'drain_type_L',
        'drain_type_R',
        'terrain',
        'land_use_L',
        'land_use_R',
        'impassable',
        'impassable_reason',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    // Relasi ke Kabupaten
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
    }

    public function linkNo()
    {
        return $this->belongsTo(Link::class, 'link_no', 'link_no');
    }

     public function pavementType()
    {
        return $this->belongsTo(CodePavementType::class, 'pave_type', 'code');
    }

    public function shoulderTypeL()
    {
        return $this->belongsTo(CodePavementType::class, 'should_type_L', 'code');
    }

    public function shoulderTypeR()
    {
        return $this->belongsTo(CodePavementType::class, 'should_type_R', 'code');
    }

    public function drainTypeL()
    {
        return $this->belongsTo(CodeDrainType::class, 'drain_type_L', 'code');
    }

    public function drainTypeR()
    {
        return $this->belongsTo(CodeDrainType::class, 'drain_type_R', 'code');
    }

    public function terrainType()
    {
        return $this->belongsTo(CodeTerrain::class, 'terrain', 'code');
    }

    public function landUseL()
    {
        return $this->belongsTo(CodeLandUse::class, 'land_use_L', 'code');
    }

    public function landUseR()
    {
        return $this->belongsTo(CodeLandUse::class, 'land_use_R', 'code');
    }

    public function impassableReason()
    {
        return $this->belongsTo(CodeImpassable::class, 'impassable_reason', 'code');
    }
}
