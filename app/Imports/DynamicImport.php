<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DynamicImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    protected $modelClass;
    protected $columns;

    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
        
        // Ambil semua kolom dari tabel
        $model = new $modelClass;
        $this->columns = Schema::getColumnListing($model->getTable());
    }

    /**
     * Proses setiap row dari Excel
     */
    public function model(array $row)
    {
        // Cek apakah ID sudah ada (validasi duplikat)
        if (isset($row['id']) && !empty($row['id'])) {
            $exists = $this->modelClass::find($row['id']);
            
            if ($exists) {
                // Skip jika ID sudah ada
                Log::info("Data dengan ID {$row['id']} sudah ada, dilewati.");
                return null;
            }
        }

        // Siapkan data untuk diinsert
        $data = [];
        foreach ($this->columns as $column) {
            // Skip kolom timestamps jika auto-generate
            if (in_array($column, ['created_at', 'updated_at'])) {
                continue;
            }
            
            if (isset($row[$column])) {
                $data[$column] = $row[$column];
            }
        }

        // Buat data baru
        try {
            return new $this->modelClass($data);
        } catch (\Exception $e) {
            Log::error("Error import data: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validasi data
     */
    public function rules(): array
    {
        return [
            '*.id' => 'nullable|integer',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            'id.integer' => 'ID harus berupa angka',
        ];
    }

    /**
     * Mulai dari baris ke berapa (karena row 1 adalah header)
     */
    public function headingRow(): int
    {
        return 1;
    }
}