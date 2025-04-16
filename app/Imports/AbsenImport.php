<?php

namespace App\Imports;

use App\Models\Absen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AbsenImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Absen([
            'user_id' => $row['user_id'],
            'tanggal' => $row['tanggal'],
            'waktu_masuk' => $row['waktu_masuk'],
            'waktu_pulang' => $row['waktu_pulang'],
            'status' => $row['status'],
            'keterangan' => $row['keterangan'],
        ]);
    }
}