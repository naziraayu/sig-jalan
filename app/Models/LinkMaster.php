<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkMaster extends Model
{
    protected $table = 'link_master';
    
    protected $fillable = [
        'link_name',
        'link_no',
        'province_code',
        'kabupaten_code',
    ];

    // Relasi ke Link (multiple years)
    public function links()
    {
        return $this->hasMany(Link::class, 'link_master_id', 'id');
    }

    public function linkKecamatans()
    {
        return $this->hasMany(LinkKecamatan::class, 'link_master_id', 'id');
    }

    public function kecamatans()
    {
        return $this->belongsToMany(
            Kecamatan::class,
            'link_kecamatan',
            'link_master_id',
            'kecamatan_code',
            'id',
            'kecamatan_code'
        );
    }

    public function roadConditions()
    {
        return $this->hasManyThrough(
            RoadCondition::class,
            Link::class,
            'link_master_id', // FK di tabel link → link_master
            'link_id',        // FK di tabel road_condition → link
            'id',             // ✅ Primary key LinkMaster
            'id'              // Primary key Link
        );
    }

    public function alignments()
    {
        return $this->hasMany(Alignment::class, 'link_master_id', 'id');
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

    // Get link by specific year
    public function linkByYear($year)
    {
        return $this->links()->where('year', $year)->first();
    }

    // Get all years available
    public function availableYears()
    {
        return $this->links()->distinct()->pluck('year')->sort()->values();
    }

    // Scope: Active only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}