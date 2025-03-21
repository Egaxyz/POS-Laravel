<?php

namespace App\Exports;

use App\Models\AjukanMenu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PengajuanExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AjukanMenu::select('nama_makanan','kategori', 'status', 'tanggal', 'deskripsi')->get();
    }
    public function headings(): array
    {
        return ["Nama Makanan", "Kategori",'Status', "Tanggal Pengajuan", 'Deskripsi'];
    }
}