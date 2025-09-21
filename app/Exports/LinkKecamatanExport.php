<?php

namespace App\Exports;

use App\Models\LinkKecamatan;
use Maatwebsite\Excel\Concerns\FromCollection;

class LinkKecamatanExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LinkKecamatan::all();
    }
}
