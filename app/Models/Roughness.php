<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roughness extends Model
{
    protected $table = 'roughness';
    public $incrementing = false;   // karena tidak ada PK auto increment
    protected $primaryKey = null;   // tidak ada primary key
    // protected $keyType = 'string';  // default tipe key (tidak digunakan)

    protected $fillable = [
        'year',
        'province_code',
        'kabupaten_code',
        'link_no',
        'chainage_from',
        'chainage_to',
        'drp_from',
        'offset_from',
        'drp_to',
        'offset_to',
        'iri',
        'analysis_base_year',
    ];
}
