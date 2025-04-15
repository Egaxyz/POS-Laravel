<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PegawaiImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function Model(array $row){
        return new User([
            'nama' => $row['nama'],
            'password' => bcrypt($row['password']),
            'no_hp'=> $row['no_hp'],
            'status'=> strtolower(trim($row['status'])),
            'role'     => strtolower(trim($row['role'])),
        ]);
    }
}