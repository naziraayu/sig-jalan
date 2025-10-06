<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    // Nama tabel (karena kamu pakai "link" bukan "links")
    protected $table = 'link';

    // Primary key
    protected $primaryKey = 'link_no';
    public $incrementing = false; // karena bukan auto increment
    protected $keyType = 'string'; // tipe string

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'link_no',
        'province_code',
        'kabupaten_code',
        'link_code',
        'link_name',
        'status',
        'function',
        'class',
        'project_number',
        'access_status',
        'link_length_official',
        'link_length_actual',
        'WTI',
        'MCA2',
        'MCA3',
        'MCA4',
        'MCA5',
        'CUMESA',
        'ESA0',
        'AADT',
    ];

    // Relasi ke Province
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    // Relasi ke Kabupaten
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
    }

    public function statusRelation() {
        return $this->belongsTo(CodeLinkStatus::class, 'status', 'code');
    }

    // Link.php
    public function functionRelation()
    {
        return $this->belongsTo(CodeLinkFunction::class, 'function', 'code');
    }

    public function drp()
    {
        return $this->hasMany(DRP::class, 'link_no', 'link_no');
    }

    // Scope untuk ruas yang belum memiliki DRP
    public function scopeWithoutDRP($query)
    {
        return $query->whereDoesntHave('drp');
    }
    
    // Scope untuk ruas yang sudah memiliki DRP  
    public function scopeWithDRP($query)
    {
        return $query->whereHas('drp');
    }

    public function inventories()
    {
        return $this->hasMany(RoadInventory::class, 'link_no', 'link_no');
    }

    public function conditions()
    {
        return $this->hasMany(RoadCondition::class, 'link_no', 'link_no');
    }


}
