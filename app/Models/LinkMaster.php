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