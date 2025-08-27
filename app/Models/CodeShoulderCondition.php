<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeShoulderCondition extends Model
{
    protected $table = "code_shoulder_condition";
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code', 
        'code_description_eng',
        'code_description_ind',
        'order',
    ];

    //shoulder table
    public function shoulderL() {
        return $this->belongsTo(RoadCondition::class, 'shoulder_l', 'code');
    }   
    public function shoulderR() {
        return $this->belongsTo(RoadCondition::class, 'shoulder_r', 'code');
    }  
}
