<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Schema;

class DynamicExport implements FromCollection, WithHeadings, WithMapping
{
    protected $modelClass;
    protected $columns;

    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
        
        // Ambil semua kolom dari tabel database
        $model = new $modelClass;
        $allColumns = Schema::getColumnListing($model->getTable());
        
        // Filter: Hapus kolom created_at dan updated_at
        $this->columns = array_filter($allColumns, function($column) {
            return !in_array($column, ['created_at', 'updated_at']);
        });
        
        // Reset array keys agar berurutan
        $this->columns = array_values($this->columns);
    }

    /**
     * Ambil semua data dari model
     */
    public function collection()
    {
        // Hanya select kolom yang dibutuhkan (tanpa created_at & updated_at)
        return $this->modelClass::select($this->columns)->get();
    }

    /**
     * Header kolom Excel (nama kolom database tanpa timestamps)
     */
    public function headings(): array
    {
        return $this->columns;
    }

    /**
     * Map data sesuai urutan kolom
     */
    public function map($row): array
    {
        $data = [];
        foreach ($this->columns as $column) {
            $data[] = $row->$column;
        }
        return $data;
    }
}