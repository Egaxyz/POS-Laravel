<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user')->insert([
            [
                'nama' => 'Ega',
                'password' => Hash::make('admin123'),
                'role' => 'manager',
                'status' => 'aktif',
                'no_hp' => '081234567890',
            ],
            [
                'nama' => 'Gunawan',
                'password' => Hash::make('admin123'),
                'role' => 'karyawan',
                'status' => 'aktif',
                'no_hp' => '081234567891',
            ],
            [
                'nama' => 'Novaldi',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'aktif',
                'no_hp' => '081234567892',
            ],
            [
                'nama' => 'Ezio',
                'password' => Hash::make('admin123'),
                'role' => 'member',
                'status' => 'aktif',
                'no_hp' => '081234567893',
            ],
        ]);
    }
}