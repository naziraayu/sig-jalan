<?php

namespace App\Models;

use App\Models\CodeLinkFunction;
use App\Models\CodeLinkStatus;
use App\Models\DRP;
use App\Models\Kabupaten;
use App\Models\LinkMaster;
use App\Models\Province;
use App\Models\RoadCondition;
use App\Models\RoadInventory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'link';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'year',
        'province_code',
        'kabupaten_code',
        'link_no',
        'link_master_id',
        'link_code',
        'status',
        'function',
        'class',
        'project_number',
        'access_status',
        'link_length_official',
        'link_length_actual',
        'WTI',
        'MCA2',
        'MCA3',
        'MCA4',
        'MCA5',
        'CUMESA',
        'ESA0',
        'AADT',
    ];

    protected $casts = [
        'year' => 'integer',
        'link_length_official' => 'decimal:2',
        'link_length_actual' => 'decimal:2',
    ];

    public function linkMaster()
    {
        return $this->belongsTo(LinkMaster::class, 'link_master_id', 'id');
    }


    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
    }

    public function statusRelation()
    {
        return $this->belongsTo(CodeLinkStatus::class, 'status', 'code');
    }

    public function functionRelation()
    {
        return $this->belongsTo(CodeLinkFunction::class, 'function', 'code');
    }

    public function drp()
    {
        return $this->hasMany(DRP::class, 'link_id', 'id');
    }

    public function roadInventories()
    {
        return $this->hasMany(RoadInventory::class, 'link_id', 'id');
    }

    public function roadConditions()
    {
        return $this->hasMany(RoadCondition::class, 'link_id', 'id');
    }

    // ====================================
    // ✅ SCOPES
    // ====================================

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeWithMaster($query)
    {
        return $query->with('linkMaster');
    }

    public function scopeCurrentYear($query)
    {
        $year = session('selected_year', now()->year);
        return $query->where('year', $year);
    }

    public function scopeWithoutDRP($query)
    {
        return $query->whereDoesntHave('drp');
    }

    public function scopeWithDRP($query)
    {
        return $query->whereHas('drp');
    }

    // ====================================
    // ✅ ACCESSORS
    // ====================================
    public function getLinkNameAttribute()
    {
        return $this->linkMaster?->link_name;
    }

}