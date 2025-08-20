<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $primaryKey = 'province_code';
    public $incrementing = false;   // karena PK bukan auto increment
    protected $keyType = 'string'; // tipe PK adalah string

    protected $fillable = [
        'province_code',
        'province_name',
        'default_province',
        'stable',
    ];
}
