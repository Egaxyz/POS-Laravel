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
     * @brief Menampilkan daftar pengajuan menu.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /// Mengambil data pengajuan menu, diurutkan berdasarkan status ('pending' diutamakan) dan tanggal terbaru
        $data = AjukanMenu::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        /// Mendapatkan data pengguna yang sedang login
        $user = auth()->user();

        /// Menampilkan view berdasarkan peran pengguna
        if ($user->role == 'member') {
            return view('Member/Pengajuan/index', compact('data'));
        } elseif ($user->role == 'karyawan') {
            return view('Karyawan/Pengajuan/index', compact('data'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * @brief Menyimpan pengajuan menu baru.
     * 
     * @param Request $request Data request dari form pengajuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /// Mengambil ID pengguna yang sedang login
        $userId = auth()->id() ?? 1;

        /// Menyimpan data pengajuan menu ke database
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

        /// Redirect berdasarkan peran
        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Menu Berhasil Diajukan');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * @brief Memperbarui data pengajuan menu.
     * 
     * @param Request $request Data request dari form
     * @param int $id ID dari pengajuan yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $id)
    {
        /// Mencari data pengajuan
        $data = AjukanMenu::findOrFail($id);

        /// Update data
        $data->nama_makanan = $request->nama_makanan;
        $data->kategori = $request->kategori;
        $data->deskripsi = $request->deskripsi;
        $data->tanggal = now();
        $data->save();

        /// Mencatat log
        Log::info("Pengajuan ID: $id diperbarui oleh user ID: " . auth()->id());

        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Pengajuan Berhasil Diperbarui');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * @brief Menghapus data pengajuan menu.
     * 
     * @param int $id ID dari pengajuan yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        /// Mencari data dan menghapusnya
        $data = AjukanMenu::findOrFail($id);
        $namaMakanan = $data->nama_makanan;
        $data->delete();

        /// Mencatat log
        Log::info("Pengajuan ID: $id ($namaMakanan) dihapus oleh user ID: " . auth()->id());

        $user = auth()->user();
        if ($user->role == 'member') {
            return redirect()->route('member.ajukan')->with('success', 'Pengajuan Berhasil Dihapus');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * @brief Menyetujui pengajuan dan menambahkannya ke menu.
     * 
     * @param Request $request Request yang mengandung data dan file gambar
     * @param int $id ID dari pengajuan yang disetujui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selesai(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            /// Validasi gambar
            $request->validate([
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            /// Mencari data pengajuan
            $ajukanMenu = AjukanMenu::findOrFail($id);

            /// Simpan gambar jika ada
            if ($request->hasFile('gambar')) {
                $imageName = time() . '.' . $request->gambar->extension();
                $request->gambar->move(public_path('uploads/menu'), $imageName);
                $ajukanMenu->update(['gambar' => $imageName]);
            }

            /// Cek status sebelumnya
            if ($ajukanMenu->status === 'Selesai') {
                return redirect()->back()->with('error', 'Pengajuan sudah selesai sebelumnya.');
            }

            /// Update status
            $ajukanMenu->status = 'disetujui';
            $ajukanMenu->save();

            /// Tambahkan ke menu
            $menu = new Menu();
            $menu->nama_makanan = $ajukanMenu->nama_makanan;
            $menu->deskripsi = $ajukanMenu->deskripsi;
            $menu->harga = $ajukanMenu->harga;
            $menu->kategori = $ajukanMenu->kategori;
            $menu->stok = $ajukanMenu->stok;
            $menu->user_id = auth()->id();
            $menu->save();

            /// Log
            Log::info("Pengajuan ID: $id disetujui dan ditambahkan ke menu oleh user ID: " . auth()->id());

            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan Telah Diselesaikan dan Menu Ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyelesaikan pengajuan ID: $id - " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * @brief Membatalkan pengajuan menu.
     * 
     * @param int $id ID pengajuan yang ingin dibatalkan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batal($id)
    {
        /// Ubah status pengajuan menjadi 'ditolak'
        $data = AjukanMenu::findOrFail($id);
        $data->status = 'ditolak';
        $data->save();

        /// Log
        Log::info("Pengajuan ID: $id ditolak oleh user ID: " . auth()->id());

        return redirect()->back()->with('success', 'Pengajuan Telah Digagalkan.');
    }

    /**
     * @brief Mengekspor daftar pengajuan menu ke dalam file Excel.
     * 
     * @param Excel $excel Objek Excel untuk proses export
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Excel $excel)
    {
        return $excel->download(new PengajuanExport, 'Laporan_Pengajuan.xlsx');
    }

}