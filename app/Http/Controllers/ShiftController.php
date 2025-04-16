<?php

namespace App\Http\Controllers;

use App\Exports\AbsenExport;
use App\Imports\AbsenImport;
use App\Models\Absen;
use App\Models\Shift;
use App\Models\ShiftUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ShiftController extends Controller
{
    public function absen()
{
    // Pastikan hanya mengambil data karyawan, bukan semua user
    $akun = User::where('role', 'karyawan')->get(); // atau role sesuai sistem Anda
    $user = Absen::orderBy('tanggal')
            ->with('user') // cukup ini saja
            ->paginate(5);
    // $absen = Absen::with('user')->get();
        // dd($akun);

    return view('Manager/Absensi/index', compact('user', 'akun')); 
}
    public function absenMasuk(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:user,id',
    ]);

    // Ambil tanggal dari request, default ke hari ini jika tidak disediakan
    $tanggal = Carbon::parse($request->tanggal)->toDateString();

    // Cek apakah user sudah absen di tanggal yang sama
    $absen = Absen::where('user_id', $request->user_id)
                  ->where('tanggal', $tanggal)
                  ->first();

    if ($absen) {        
        return response()->json(['message' => 'Karyawan sudah absen pada tanggal tersebut'], 404);
    }
 // Tentukan waktu pulang berdasarkan status
    $waktuPulang = ($request->status == 'hadir') 
        ? Carbon::parse($request->waktu_pulang)->toTimeString() 
        : '00:00:00';

    // Simpan absen jika belum ada
    Absen::create([
        'user_id' => $request->user_id,
        'tanggal' => $tanggal,
        'waktu_masuk' => Carbon::parse($request->waktu_masuk)->toTimeString(),
        'waktu_pulang' => $waktuPulang,
        'status' => $request->status,
        'keterangan'=>$request->keterangan
    ]);

    return redirect()->back()->with('success', 'Absen Telah Berhasil');
}

public function updateAbsen(Request $request, $id)
{
    $request->validate([
        'tanggal' => 'required',
        'waktu_masuk' => 'nullable|date_format:H:i:s',
        'waktu_pulang' => 'nullable|date_format:H:i:s',
        'status' => 'nullable|string',
    ]);

    $absen = Absen::find($id);

    if (!$absen) {
        return response()->json(['message' => 'Data absen tidak ditemukan'], 404);
    }

    // Cek status dan set waktu_pulang jika status adalah 'sakit' atau 'cuti'
    if (in_array($request->status, ['sakit', 'cuti'])) {
        // Set waktu_pulang menjadi tanggal saat ini dengan jam 00:00:00
        $request->merge(['waktu_pulang' => now()->toDateString() . ' 00:00:00']); 
    }

    $absen->update([
        'tanggal' => $request->tanggal ?? $absen->tanggal,
        'waktu_masuk' => $request->waktu_masuk ?? $absen->waktu_masuk,
        'waktu_pulang' => $request->waktu_pulang ?? $absen->waktu_pulang,
        'status' => $request->status ?? $absen->status,
    ]);
    return redirect()->back()->with('success', 'Absen Telah Diperbarui');
}


public function deleteAbsen($id)
{
    $absen = Absen::find($id);

    if (!$absen) {
        return response()->json(['message' => 'Data absen tidak ditemukan'], 404);
    }

    $absen->delete();

    return redirect()->back()->with('success', 'Absen Telah Dihapus');
}
  public function exportExcel()
    {
        return Excel::download(new AbsenExport, 'absen.xlsx');
    }

     public function showImportForm()
{
    return view('absen.import');
}
public function markAsDone($id)
{
    $absen = Absen::findOrFail($id);
    
    // Validate that the current time is past the clock-out time
    $waktuPulang = Carbon::parse($absen->tanggal . ' ' . $absen->waktu_pulang);
    
    if (now()->lt($waktuPulang)) {
        return response()->json([
            'success' => false,
            'message' => 'Belum waktunya pulang'
        ], 400);
    }
    
    $absen->update(['is_selesai' => '1']);
    
    return response()->json([
        'success' => true,
        'message' => 'Status absensi berhasil diupdate'
    ]);
}
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new AbsenImport, $request->file('file'));

    return back()->with('success', 'Data Pegawai berhasil diimpor!');
}
}