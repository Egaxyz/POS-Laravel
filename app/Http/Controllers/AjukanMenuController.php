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
    /**
     * Menampilkan daftar pengajuan menu.
     */
    public function index()
    {
        // Mengambil data pengajuan menu, diurutkan berdasarkan status ('pending' diutamakan) dan tanggal terbaru
        $data = AjukanMenu::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        // Mendapatkan data pengguna yang sedang login
        $user = auth()->user();

        // Menampilkan view berdasarkan peran pengguna
        if ($user->role == 'member') {
            return view('Member/Pengajuan/index', compact('data'));
        } elseif ($user->role == 'karyawan') {
            return view('Karyawan/Pengajuan/index', compact('data'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Menyimpan pengajuan menu baru.
     */
    public function store(Request $request)
    {
        // Mengambil ID pengguna yang sedang login
        $userId = auth()->id() ?? 1;

        // Menyimpan data pengajuan menu ke database
        $data = AjukanMenu::create([
            'user_id' => $userId,
            'nama_makanan' => $request->nama_makanan,
            'harga' => 0, // Harga default 0
            'stok' => 0, // Stok default 0
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'tanggal' => now(),
            'status' => 'pending',
        ]);

        // Mencatat log bahwa pengajuan baru telah ditambahkan
        Log::info("Pengajuan baru ditambahkan oleh user ID: $userId - {$request->nama_makanan}");

        // Redirect ke halaman pengajuan dengan pesan sukses jika user adalah 'member'
        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Menu Berhasil Diajukan');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Memperbarui data pengajuan menu.
     */
    public function edit(Request $request, $id)
    {
        // Mencari data pengajuan berdasarkan ID
        $data = AjukanMenu::findOrFail($id);
        
        // Memperbarui data pengajuan
        $data->nama_makanan = $request->nama_makanan;
        $data->kategori = $request->kategori;
        $data->deskripsi = $request->deskripsi;
        $data->tanggal = now();
        $data->save();

        // Mencatat log bahwa pengajuan diperbarui
        Log::info("Pengajuan ID: $id diperbarui oleh user ID: " . auth()->id());

        // Redirect dengan pesan sukses jika user adalah 'member'
        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Pengajuan Berhasil Diperbarui');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Menghapus data pengajuan menu.
     */
    public function destroy($id)
    {
        // Mencari data pengajuan berdasarkan ID dan menghapusnya
        $data = AjukanMenu::findOrFail($id);
        $namaMakanan = $data->nama_makanan;
        $data->delete();

        // Mencatat log bahwa pengajuan telah dihapus
        Log::info("Pengajuan ID: $id ($namaMakanan) dihapus oleh user ID: " . auth()->id());

        // Redirect dengan pesan sukses jika user adalah 'member'
        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Pengajuan Berhasil Dihapus');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Menyetujui pengajuan dan menambahkannya ke menu.
     */
    public function selesai(Request $request, $id)
    {
        DB::beginTransaction(); // Memulai transaksi database
        try {
            // Validasi input gambar
            $request->validate([
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Mencari data pengajuan berdasarkan ID
            $ajukanMenu = AjukanMenu::findOrFail($id);

            // Jika ada gambar, simpan gambar ke folder uploads/menu
            if ($request->hasFile('gambar')) {
                $imageName = time() . '.' . $request->gambar->extension();
                $request->gambar->move(public_path('uploads/menu'), $imageName);
                $ajukanMenu->update(['gambar' => $imageName]);
            }

            // Jika pengajuan sudah selesai sebelumnya, beri pesan error
            if ($ajukanMenu->status === 'Selesai') {
                return redirect()->back()->with('error', 'Pengajuan sudah selesai sebelumnya.');
            }

            // Mengubah status pengajuan menjadi 'disetujui'
            $ajukanMenu->status = 'disetujui';
            $ajukanMenu->save();

            // Menambahkan menu ke dalam tabel Menu
            $menu = new Menu();
            $menu->nama_makanan = $ajukanMenu->nama_makanan;
            $menu->deskripsi = $ajukanMenu->deskripsi;
            $menu->harga = $ajukanMenu->harga;
            $menu->kategori = $ajukanMenu->kategori;
            $menu->stok = $ajukanMenu->stok;
            $menu->user_id = auth()->id();
            $menu->save();

            // Mencatat log bahwa pengajuan telah disetujui
            Log::info("Pengajuan ID: $id disetujui dan ditambahkan ke menu oleh user ID: " . auth()->id());

            DB::commit(); // Menyimpan perubahan ke database
            return redirect()->back()->with('success', 'Pengajuan Telah Diselesaikan dan Menu Ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack(); // Mengembalikan perubahan jika terjadi kesalahan
            Log::error("Gagal menyelesaikan pengajuan ID: $id - " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Membatalkan pengajuan menu.
     */
    public function batal($id)
    {
        // Mencari pengajuan berdasarkan ID dan mengubah statusnya menjadi 'ditolak'
        $data = AjukanMenu::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        // Mencatat log bahwa pengajuan ditolak
        Log::info("Pengajuan ID: $id ditolak oleh user ID: " . auth()->id());

        return redirect()->back()->with('success', 'Pengajuan Telah Digagalkan.');
    }

    /**
     * Mengekspor daftar pengajuan menu ke dalam file Excel.
     */
    public function exportExcel(Excel $excel)
    {
        return $excel->download(new PengajuanExport, 'Laporan_Pengajuan.xlsx');
    }
}
