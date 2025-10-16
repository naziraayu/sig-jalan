<?php

namespace App\Exports;

use App\Models\LinkMaster;
use Maatwebsite\Excel\Concerns\FromCollection;

class LinkMasterExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LinkMaster::all();
    }
}
