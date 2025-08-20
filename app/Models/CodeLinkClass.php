<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeLinkClass extends Model
{
    protected $table = "code_link_class";
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
