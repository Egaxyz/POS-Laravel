<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penjualan')->insert([
            [
                'user_id' => 1,
                'no_faktur' => 'PSN2503001',
                'total_harga' => 50000,
                'tanggal' => Carbon::now(),
                'metode_pembayaran' => 'Cash',
                'status_penjualan' => 'Proses',
            ],
        ]);
    }
}