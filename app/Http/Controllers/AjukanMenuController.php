<?php

namespace App\Http\Controllers;

use App\Exports\PengajuanExport;
use App\Models\AjukanMenu;
use App\Models\Menu;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class AjukanMenuController extends Controller
{
    public function index(){
        $data = AjukanMenu::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        $user = auth()->user();
        if ($user->role == 'member') {
            return view('Member/Pengajuan/index',compact('data'));
        } elseif ($user->role == 'karyawan') {
            return view('Karyawan/Pengajuan/index',compact('data'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function store(Request $request){
        $userId = auth()->id() ?? 1;

        $data = AjukanMenu::create([
            'user_id' => $userId,
            'nama_makanan' => $request->nama_makanan,
            'harga' => 0,
            'stok' => 0,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'tanggal' => now(),
            'status' => 'pending',
        ]);

        Log::info("Pengajuan baru ditambahkan oleh user ID: $userId - {$request->nama_makanan}");

        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Menu Berhasil Diajukan');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function edit(Request $request, $id){
        $data = AjukanMenu::findOrFail($id);
        $data->nama_makanan = $request->nama_makanan;
        $data->kategori = $request->kategori;
        $data->deskripsi = $request->deskripsi;
        $data->tanggal = now();
        $data->save();

        Log::info("Pengajuan ID: $id diperbarui oleh user ID: " . auth()->id());

        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Pengajuan Berhasil Diperbarui');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function destroy($id){
        $data = AjukanMenu::findOrFail($id);
        $namaMakanan = $data->nama_makanan;
        $data->delete();

        Log::info("Pengajuan ID: $id ($namaMakanan) dihapus oleh user ID: " . auth()->id());

        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Pengajuan Berhasil Dihapus');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function selesai(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $ajukanMenu = AjukanMenu::findOrFail($id);

            if ($request->hasFile('gambar')) {
                $imageName = time() . '.' . $request->gambar->extension();
                $request->gambar->move(public_path('uploads/menu'), $imageName);
                $ajukanMenu->update(['gambar' => $imageName]);
            }

            if ($ajukanMenu->status === 'Selesai') {
                return redirect()->back()->with('error', 'Pengajuan sudah selesai sebelumnya.');
            }

            $ajukanMenu->status = 'disetujui';
            $ajukanMenu->save();

            $menu = new Menu();
            $menu->nama_makanan = $ajukanMenu->nama_makanan;
            $menu->deskripsi = $ajukanMenu->deskripsi;
            $menu->harga = $ajukanMenu->harga;
            $menu->kategori = $ajukanMenu->kategori;
            $menu->stok = $ajukanMenu->stok;
            $menu->user_id = auth()->id();
            $menu->save();

            Log::info("Pengajuan ID: $id disetujui dan ditambahkan ke menu oleh user ID: " . auth()->id());

            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan Telah Diselesaikan dan Menu Ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyelesaikan pengajuan ID: $id - " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function batal($id)
    {
        $data = AjukanMenu::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        Log::info("Pengajuan ID: $id ditolak oleh user ID: " . auth()->id());

        return redirect()->back()->with('success', 'Pengajuan Telah Digagalkan.');
    }

    public function exportExcel(Excel $excel){
        Log::info("User ID: " . auth()->id() . " mengunduh laporan pengajuan dalam format Excel.");

        return $excel->download(new PengajuanExport, 'Laporan_Pengajuan.xlsx');
    }
}