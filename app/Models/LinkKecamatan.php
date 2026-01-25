<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkKecamatan extends Model
{
    protected $table = 'link_kecamatan';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'province_code',
        'kabupaten_code',
        'link_id',
        'drp_from',
        'drp_to',
        'kecamatan_code',
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

    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }

    /**
     * ✅ TAMBAHAN: Relasi ke RoadCondition melalui Link
     */
    public function roadConditions()
    {
        return $this->hasManyThrough(
            RoadCondition::class,
            Link::class,
            'id',        // FK di tabel link
            'link_id',   // FK di tabel road_condition
            'link_id',   // Local key di link_kecamatan
            'id'         // Local key di link
        );
    }

    /// Relasi ke titik DRP awal - dengan filter link_no
    public function drpFrom()
    {
        return $this->belongsTo(Drp::class, 'drp_from', 'drp_num')
                    ->where('drp.link_no', '=', $this->attributes['link_no'] ?? null);
    }   
    
    // Relasi ke titik DRP akhir - dengan filter link_no  
    public function drpTo()
    {
        return $this->belongsTo(Drp::class, 'drp_to', 'drp_num')
                    ->where('drp.link_no', '=', $this->attributes['link_no'] ?? null);
    }
    
     public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_code', 'kecamatan_code');
    }
}
