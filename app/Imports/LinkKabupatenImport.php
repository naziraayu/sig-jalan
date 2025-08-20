<?php

namespace App\Imports;

use App\Models\LinkKabupaten;
use Maatwebsite\Excel\Concerns\ToModel;

class LinkKabupatenImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new LinkKabupaten([
            //
        ]);
    }
}
