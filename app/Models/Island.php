<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Island extends Model
{
    protected $table = "island";
    protected $primaryKey = 'island_code';
    public $incrementing = false;   // karena PK bukan auto increment
    protected $keyType = 'string'; // tipe PK adalah string

    protected $fillable = [
        'island_code',
        'province_code',
        'island_name',
    ];
}
