<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Supplier::all();
    
    }

    public function headings(): array
    {
        return [
            'Id',
            'Nama Perusahaan',
            'Kontak',
            'Alamat',
            'Email',
            'Status',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->nama_perusahaan,
            $supplier->kontak,
            $supplier->alamat,
            $supplier->email,
            $supplier->status,
        ];
    }
}