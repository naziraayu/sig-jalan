<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DRP extends Model
{
    protected $table = 'drp';
    // Tidak ada primary key
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';
    // Kolom yang bisa diisi mass-assignment
    protected $fillable = [
        'province_code',
        'kabupaten_code',
        'link_no',
        'drp_num',
        'chainage',
        'drp_order',
        'drp_length',
        'dpr_north_deg',
        'dpr_north_min',
        'dpr_north_sec',
        'dpr_east_deg',
        'dpr_east_min', 
        'dpr_east_sec',
        'drp_type',
        'drp_desc',
        'drp_comment',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
    }

    public function type()
    {
        return $this->belongsTo(CodeDrpType::class, 'drp_type', 'code');
    }
    // Relasi ke link_kabupaten sebagai titik awal
    public function linkFrom()
    {
        return $this->hasMany(LinkKabupaten::class, 'drp_from', 'drp_num');
    }

    // Relasi ke link_kabupaten sebagai titik akhir
    public function linkTo()
    {
        return $this->hasMany(LinkKabupaten::class, 'drp_to', 'drp_num');
    }
    public function kecFrom()
    {
        return $this->hasMany(LinkKecamatan::class, 'drp_from', 'drp_num');
    }
    public function kecTo()
    {
        return $this->hasMany(LinkKecamatan::class, 'drp_to', 'drp_num');
    }
    public function status()
    {
        return $this->belongsTo(CodeLinkStatus::class, 'status_code', 'code');
    }

    public function link()
    {
        return $this->belongsTo(Link::class, 'link_no', 'link_no');
    }

}
