<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('supplier')->insert([
            [
                'nama_perusahaan' => 'PT. MENCARI CINTA SEJATI',
                'kontak' => '0801',
                'alamat' => 'Jl. Semawur',
                'email' => 'ega@black.com',
                'status' => 'aktif',
            ],
            [
                'nama_perusahaan' => 'PT. Kecap',
                'kontak' => '123',
                'alamat' => 'Jalan Jalan',
                'email' => 'jkw@yahoo.com',
                'status' => 'aktif',
            ],
        ]);
    }
}