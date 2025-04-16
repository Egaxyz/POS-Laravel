<?php

namespace App\Exports;

use App\Models\Absen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsenExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Absen::all(); // Ambil semua data dari tabel absensi
    }

    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Tanggal',
            'Waktu Masuk',
            'Waktu Pulang',
            'Status',
            'Keterangan',
            'Created At',
            'Updated At',
        ];
    }

    public function map($absen): array
    {
        return [
            $absen->id,
            $absen->user_id,
            $absen->tanggal,
            $absen->waktu_masuk,
            $absen->waktu_pulang,
            $absen->status,
            $absen->keterangan,
            $absen->created_at,
            $absen->updated_at,
        ];
    }
}