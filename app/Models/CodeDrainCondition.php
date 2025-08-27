<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeDrainCondition extends Model
{
    protected $table = "code_drain_condition";
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'code_description_eng',
        'code_description_ind',
        'order',
    ];

    //drain table
    public function drainL() {
        return $this->belongsTo(RoadCondition::class, 'drain_l', 'code');
    }   
    public function drainR() {
        return $this->belongsTo(RoadCondition::class, 'drain_r', 'code');
    }  
}
