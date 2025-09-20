<?php

namespace App\Exports;

use App\Models\DRP;
use Maatwebsite\Excel\Concerns\FromCollection;

class DRPExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DRP::all();
    }
}
