<?php

namespace App\Models;

use App\Models\CodeDrainType;
use App\Models\CodeImpassable;
use App\Models\CodeLandUse;
use App\Models\CodePavementType;
use App\Models\CodeTerrain;
use App\Models\Kabupaten;
use App\Models\Link;
use App\Models\LinkMaster;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

class RoadInventory extends Model
{
    protected $table = "road_inventory";
    protected $primaryKey = 'id'; // <- biasanya pakai id, tapi ubah sesuai strukturmu
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'province_code',
        'kabupaten_code',
        'link_id',
        'link_no',
        'year',
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

    protected $casts = [
        'year' => 'integer',
        'impassable' => 'boolean',
        'chainage_from' => 'integer',
        'chainage_to' => 'integer',
    ];

    // 🔗 Relasi ke Link
    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }

    // 🔗 Relasi ke LinkMaster melalui Link
    public function linkMaster()
    {
        return $this->hasOneThrough(
            LinkMaster::class,
            Link::class,
            'id',          // Foreign key di tabel link
            'id',          // Primary key di tabel link_master
            'link_id',     // Foreign key di tabel road_inventory
            'link_master_id' // Foreign key di tabel link
        );
    }

    // 🔗 Relasi ke tabel referensi
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
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

    // Scope filter
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['link', 'link.linkMaster']);
    }
}
