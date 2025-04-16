<?php

namespace Database\Seeders;

use App\Models\Absen;
use Illuminate\Database\Seeder;
use App\Models\Absensi;
use Carbon\Carbon;

class AbsensSeeder extends Seeder
{
    public function run(): void
    {
        Absen::create([
            'user_id' => 1,
            'tanggal' => Carbon::now()->toDateString(),
            'waktu_masuk' => Carbon::now()->setTime(8, 0),
            'waktu_pulang' => Carbon::now()->setTime(17, 0),
            'status' => 'hadir',
            'keterangan' => 'Masuk tepat waktu',
        ]);

        Absen::create([
            'user_id' => 2,
            'tanggal' => Carbon::now()->subDay()->toDateString(),
            'waktu_masuk' => Carbon::now()->subDay()->setTime(8, 15),
            'waktu_pulang' => Carbon::now()->subDay()->setTime(17, 0),
            'status' => 'sakit',
            'keterangan' => 'Mengeluh demam',
        ]);

    }
}