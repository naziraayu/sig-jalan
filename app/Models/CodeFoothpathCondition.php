<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeFoothpathCondition extends Model
{
    protected $table = "code_foothpath_condition";
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'code_description_eng',
        'code_description_ind',
        'order',
    ];

    public function footpathL() {
        return $this->belongsTo(RoadCondition::class, 'footpath_l', 'code');
    }   
    public function footpathR() {
        return $this->belongsTo(RoadCondition::class, 'footpath_r', 'code');
    } 
}
