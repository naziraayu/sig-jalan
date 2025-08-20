<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
   protected $table = 'kabupaten';
    protected $primaryKey = 'kabupaten_code';
    public $incrementing = false; // karena bukan auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'kabupaten_code',
        'province_code',
        'kabupaten_name',
        'balai_code',
        'island_code',
        'default_kabupaten',
        'stable',
    ];

    // Relasi ke Province
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    // Relasi ke Balai
    public function balai()
    {
        return $this->belongsTo(Balai::class, 'balai_code', 'balai_code');
    }
}
