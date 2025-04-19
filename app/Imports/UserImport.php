<?php

namespace App\Imports;

use App\Models\User;
use Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row){
        return new User([
            'nama' => $row['nama'],
            'password'=> isset($row['password'])
            ? Hash::make($row['password']) 
            : Hash::make('admin123'),
            'no_hp'=> $row['no_hp'],
            'status'=> strtolower(trim($row['status'])),
            'role'     => strtolower(trim($row['role'])),
        ]);
    }
}