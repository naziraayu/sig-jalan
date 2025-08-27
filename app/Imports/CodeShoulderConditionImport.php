<?php

namespace App\Imports;

use App\Models\CodeShoulderCondition;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CodeShoulderConditionImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new CodeShoulderCondition([
            'code'                      => $row['code'],
            'code_description_eng'      => $row['codedescription_eng'],
            'code_description_ind'      => $row['codedescription_ind'],
            'order'                     => $row['order'],
        ]);
    }
}
