<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeSlopeCondition extends Model
{
    protected $table = "code_slope_condition";
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code', 
        'code_description_eng',
        'code_description_ind',
        'order',
    ];

    //slope table
    public function slopeL() {
        return $this->belongsTo(RoadCondition::class, 'slope_l', 'code');
    }   
    public function slopeR() {
        return $this->belongsTo(RoadCondition::class, 'slope_r', 'code');
    } 
}
