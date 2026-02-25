<?php

namespace App\Models;

use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\LinkMaster;
use App\Models\Province;
use App\Models\RoadCondition;
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
        'link_master_id',
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

    public function linkMaster()
    {
        return $this->belongsTo(LinkMaster::class, 'link_master_id', 'id');
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
