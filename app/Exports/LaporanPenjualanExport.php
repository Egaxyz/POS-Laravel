<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPenjualanExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Penjualan::select('total_harga', 'status_penjualan', 'tanggal')->get();
    }
    public function headings(): array
    {
        return ["Total Harga", "Status Penjualan", "Tanggal Penjualan"];
    }
}