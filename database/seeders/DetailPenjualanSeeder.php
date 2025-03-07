<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailPenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_penjualan')->insert([
            [
                'menu_id' => 1,
                'penjualan_id' => 12,
                'jumlah' => 1,
                'harga_satuan' => 25000,
            ],
            [
                'menu_id' => 1,
                'penjualan_id' => 12,
                'jumlah' => 1,
                'harga_satuan' => 25000,
            ],
        ]);
    }
}