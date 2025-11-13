<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alignment extends Model
{
    use HasFactory;

    protected $table = 'alignment'; 
 
    // protected $primaryKey = 'chainage'; 
    public $incrementing = false; // karena chainage bukan auto increment
    protected $keyType = 'int';  

    protected $fillable = [
        'province_code',
        'kabupaten_code',
        'link_master_id',
        'link_no',
        'year',
        'chainage',
        'chainage_rb',
        'gpspoint_north_deg',
        'gpspoint_north_min',
        'gpspoint_north_sec',
        'gpspoint_east_deg',
        'gpspoint_east_min',
        'gpspoint_east_sec',
        'section_wkt_linestring',
        'east',
        'north',
    ];

    public function linkMaster()
    {
        return $this->belongsTo(LinkMaster::class, 'link_master_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    // Relasi ke Kabupaten
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_code', 'kabupaten_code');
    }

}
