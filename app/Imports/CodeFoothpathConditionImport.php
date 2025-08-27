<?php

namespace App\Imports;

use App\Models\CodeFoothpathCondition;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CodeFoothpathConditionImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new CodeFoothpathCondition([
            'code'                      => $row['code'],
            'code_description_eng'      => $row['codedescription_eng'],
            'code_description_ind'      => $row['codedescription_ind'],
            'order'                     => $row['order'],
        ]);
    }
}
