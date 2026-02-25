<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = "kecamatan";
    protected $primaryKey = 'kecamatan_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'province_code',
        'kabupaten_code',
        'kecamatan_code',
        'kecamatan_name',
    ];

    // relasi ke province
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    // relasi ke kabupaten
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
    }
    public function links()
    {
        return $this->belongsToMany(
            LinkMaster::class,     // ✅ Ganti dari Link ke LinkMaster
            'link_kecamatan',
            'kecamatan_code',
            'link_master_id',      // ✅ Ganti dari link_id
            'kecamatan_code',
            'id'
        );
    }

    // ✅ TAMBAHAN: Relasi ke LinkKecamatan (pivot)
    public function linkKecamatans()
    {
        return $this->hasMany(LinkKecamatan::class, 'kecamatan_code', 'kecamatan_code');
    }
}
