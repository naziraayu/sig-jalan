<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodePavementType extends Model
{
    protected $table = "code_pavement_type";
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'code_description_eng',
        'code_description_ind',
        'order',
    ];
}
