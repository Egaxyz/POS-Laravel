<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToModel, WithHeadingRow
{
    public function Model(array $row){
        return new Supplier([
        'nama_perusahaan'=> $row['nama_perusahaan'],
        'kontak'=> $row['kontak'],
        'alamat'=> $row['alamat'],
        'email'=> $row['email'],
        'status'=> strtolower(trim($row['status'])),
        ]);
    }
}