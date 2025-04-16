<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absen;
use Illuminate\Support\Facades\Auth;

class AbsenController extends Controller
{
    public function absenMasuk()
    {
        // Mendapatkan karyawan yang sedang login
        $user = Auth::user();

        // Cek apakah karyawan sudah absen hari ini
        $existingAbsen = Absen::where('user_id', $user->id)
                              ->whereDate('tanggal', today())
                              ->first();

        if ($existingAbsen) {
            return response()->json(['message' => 'Anda sudah absen hari ini!'], 400);
        }

        // Jika belum absen, simpan data absen
        Absen::create([
            'user_id' => $user->id,
            'tanggal' => today(),
            'waktu_masuk' => now(),
            'status' => 'Masuk',
            'keterangan' => 'Absen masuk pada waktu: ' . now()->format('H:i:s'),
        ]);

        return response()->json(['message' => 'Absen masuk berhasil!'], 200);
    }

    public function absenPulang()
    {
        // Mendapatkan karyawan yang sedang login
        $user = Auth::user();

        // Cek apakah karyawan sudah absen hari ini dan belum pulang
        $existingAbsen = Absen::where('user_id', $user->id)
                              ->whereDate('tanggal', today())
                              ->whereNull('waktu_pulang')
                              ->first();

        if (!$existingAbsen) {
            return response()->json(['message' => 'Anda belum absen masuk atau sudah pulang!'], 400);
        }

        // Update waktu pulang
        $existingAbsen->update([
            'waktu_pulang' => now(),
            'status' => 'Pulang',
            'keterangan' => 'Absen pulang pada waktu: ' . now()->format('H:i:s'),
        ]);

        return response()->json(['message' => 'Absen pulang berhasil!'], 200);
    }
}