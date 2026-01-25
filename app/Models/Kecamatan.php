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
            Link::class,           // ✅ Model tujuan: Link
            'link_kecamatan',      // Pivot table
            'kecamatan_code',      // FK di pivot ke kecamatan
            'link_id',             // FK di pivot ke link
            'kecamatan_code',      // Local key di kecamatan
            'id'                   // Local key di link
        );
    }

    // ✅ TAMBAHAN: Relasi ke LinkKecamatan (pivot)
    public function linkKecamatans()
    {
        return $this->hasMany(LinkKecamatan::class, 'kecamatan_code', 'kecamatan_code');
    }
}
