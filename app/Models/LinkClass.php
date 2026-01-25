<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkClass extends Model
{
    protected $table = 'link_class';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'province_code',
        'kabupaten_code',
        'link_id',
        'class',
        'kmClass',
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

    public function linkNo()
    {
        return $this->belongsTo(Link::class, 'link_no', 'link_no');
    }

    public function classRelation()
    {
        return $this->belongsTo(CodeLinkClass::class, 'class', 'code');
    }

}
