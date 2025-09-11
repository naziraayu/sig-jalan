<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balai extends Model
{
    protected $table = "balai";
    protected $primaryKey = 'balai_code';
    public $incrementing = false;   // karena PK bukan auto increment
    protected $keyType = 'string'; // tipe PK adalah string

    protected $fillable = [
        'balai_code',
        'province_code',
        'balai_name',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

}
