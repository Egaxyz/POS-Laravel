<?php

namespace App\Exports;

use App\Models\BahanBaku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanBahanBakuExport implements FromCollection, WithHeadings
{
   /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      // Ambil semua user KECUALI yang role-nya 'member'
        return BahanBaku::all();
    
    }

    public function headings(): array
    {
        return [
        'Nama Perusahaan',
        'Nama Bahan',
        'Stok',
        'Satuan',
        'Harga Satuan'
        ];
    }
}