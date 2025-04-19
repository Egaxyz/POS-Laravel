<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::select("id",'nama', 'no_hp', 'status', 'role')
                     ->get();
    
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'No HP',
            'Status',
            'Role',
        ];
    }
    public function map($user): array{
        return [
            $user->id,
            $user->nama,
            $user->no_hp,
            $user->status,
            $user->role
        ];
    }
}