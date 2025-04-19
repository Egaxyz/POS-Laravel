<?php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPembelianExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Pembelian::select('total_harga', 'status_pembelian', 'tanggal_pembelian')->get();
    }
    public function headings(): array
    {
        return ["Total Harga", "Status Pembelian", "Tanggal Pembelian"];
    }
}