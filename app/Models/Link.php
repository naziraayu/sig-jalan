<?php

namespace App\Models;

use App\Models\CodeLinkFunction;
use App\Models\CodeLinkStatus;
use App\Models\DRP;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\LinkKecamatan;
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

    // ====================================
    // ✅ RELASI - SUDAH DIPERBAIKI
    // ====================================

    /**
     * Relasi ke LinkMaster (untuk ambil link_name dan link_code)
     * FK: link_master_id -> link_master.id
     */
    public function linkMaster()
    {
        return $this->belongsTo(LinkMaster::class, 'link_master_id', 'id');
    }

    /**
     * Relasi ke Province
     * FK: province_code -> provinces.province_code
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    /**
     * Relasi ke Kabupaten
     * FK: kabupaten_code -> kabupaten.kabupaten_code
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
    }

    /**
     * Relasi ke CodeLinkStatus
     * FK: status -> code_link_status.code
     */
    public function statusRelation()
    {
        return $this->belongsTo(CodeLinkStatus::class, 'status', 'code');
    }

    /**
     * Relasi ke CodeLinkFunction
     * FK: function -> code_link_function.code
     */
    public function functionRelation()
    {
        return $this->belongsTo(CodeLinkFunction::class, 'function', 'code');
    }

    /**
     * Relasi ke DRP (Detail Ruas Panggal)
     * FK: link_id -> drp.link_id
     */
    public function drp()
    {
        return $this->hasMany(DRP::class, 'link_id', 'id');
    }

    /**
     * Relasi ke RoadInventory
     * FK: link_id -> road_inventory.link_id
     */
    public function roadInventories()
    {
        return $this->hasMany(RoadInventory::class, 'link_id', 'id');
    }

    /**
     * Relasi ke RoadCondition
     * FK: link_id -> road_condition.link_id
     */
    public function roadConditions()
    {
        return $this->hasMany(RoadCondition::class, 'link_id', 'id');
    }

    public function linkKecamatans()
    {
        return $this->hasMany(LinkKecamatan::class, 'link_id', 'id');
    }

    /**
     * ✅ TAMBAHAN: Relasi Many-to-Many ke Kecamatan
     * Melalui tabel pivot link_kecamatan
     */
    public function kecamatans()
    {
        return $this->belongsToMany(
            Kecamatan::class,
            'link_kecamatan',  // Pivot table
            'link_id',         // FK di pivot yang menunjuk ke link.id
            'kecamatan_code',  // FK di pivot yang menunjuk ke kecamatan.kecamatan_code
            'id',              // Local key di link (primary key)
            'kecamatan_code'   // Local key di kecamatan
        );
    }

    // ====================================
    // ✅ SCOPES
    // ====================================

    /**
     * Scope: Filter by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope: With master relation
     */
    public function scopeWithMaster($query)
    {
        return $query->with('linkMaster');
    }
    
    /**
     * Scope: Current year from session
     */
    public function scopeCurrentYear($query)
    {
        $year = session('selected_year', now()->year);
        return $query->where('year', $year);
    }

    /**
     * Scope: Without DRP
     */
    public function scopeWithoutDRP($query)
    {
        return $query->whereDoesntHave('drp');
    }
    
    /**
     * Scope: With DRP
     */
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