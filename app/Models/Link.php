<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'link';
    
    // ✅ PERBAIKI: Primary key adalah 'id', bukan 'link_no'
    protected $primaryKey = 'id';
    public $incrementing = true; // auto increment
    protected $keyType = 'int'; // integer

    protected $fillable = [
        'year',
        'link_no', // ✅ Bukan PK, tapi unique identifier
        'link_master_id', // ✅ FK ke link_master.id
        'province_code',
        'kabupaten_code',
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

    // ✅ Relasi ke LinkMaster (untuk ambil link_name)
    public function master()
    {
        return $this->belongsTo(LinkMaster::class, 'link_master_id', 'id');
    }

    public function linkNo()
    {
        return $this->belongsTo(LinkMaster::class, 'link_no', 'link_no');
    }

    // Relasi ke Province
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    // Relasi ke Kabupaten
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
        return $this->hasMany(DRP::class, 'link_no', 'link_no')
                    ->where('year', $this->year);
    }

    public function scopeWithoutDRP($query)
    {
        return $query->whereDoesntHave('drp');
    }
    
    public function scopeWithDRP($query)
    {
        return $query->whereHas('drp');
    }

    public function roadInventories()
    {
        return $this->hasMany(RoadInventory::class, 'link_no', 'link_no')
                    ->where('year', $this->year);
    }

    public function roadConditions()
    {
        return $this->hasMany(RoadCondition::class, 'link_no', 'link_no')
                    ->where('year', $this->year);
    }

    // ✅ Accessor untuk ambil link_name dari master
    public function getLinkNameAttribute()
    {
        return $this->master?->link_name;
    }

    // Scope: Filter by year
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Scope: With master
    public function scopeWithMaster($query)
    {
        return $query->with('master');
    }
    
    // Scope: Current year from session
    public function scopeCurrentYear($query)
    {
        $year = session('selected_year', now()->year);
        return $query->where('year', $year);
    }
}