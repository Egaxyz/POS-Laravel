<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;
use DB;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::all();
        $user = auth()->user();

        // Perbarui stok menu sebelum ditampilkan
        foreach ($menu as $item) {
            $stokMenu = $this->hitungStokMenu($item->id);
            $item->stok = $stokMenu;
        }

        if ($user->role == 'superuser') {
            return view('superuser/Menu/index', compact('user', 'menu'));
        } elseif ($user->role == 'karyawan') {
            return view('karyawan/Menu/index', compact('user', 'menu'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function store(MenuRequest $request)
    {
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $filename = date('Y-m-d') . $image->getClientOriginalName();
            $path = 'menu-image/' . $filename;
            Storage::disk('public')->put($path, file_get_contents($image));
        } else {
            return back()->with('error', 'Gagal mengupload gambar. Pastikan file dipilih.');
        }

        $userId = auth()->id() ?? 1;

        $menu = Menu::create([
            'nama_makanan' => $request->nama_makanan,
            'user_id' => $userId,
            'harga' => 0, // Harga awal diatur ke 0
            'stok' => 0, // Stok awal diatur ke 0, akan dihitung otomatis
            'kategori' => $request->kategori,
            'gambar' => $filename,
            'deskripsi' => $request->deskripsi,
        ]);

        // Panggil fungsi updateMenuPrice setelah menu dibuat
        $this->updateMenuPrice($menu->id);

        // Perbarui stok menu berdasarkan bahan baku
        $this->perbaruiStokMenu($menu->id);

        $user = auth()->user();

        if ($user && $user->role == 'superuser') {
            return redirect()->route('superuser.menu')->with('success', 'Menu Berhasil Ditambah');
        } elseif ($user && $user->role == 'karyawan') {
            return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Ditambah');
        } else {
            return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Ditambah');
        }
    }

    public function updateMenuPrice($menuId)
    {
        // Hitung total harga dari bahan baku yang terkait dengan menu
        $totalHarga = DB::table('menu_bahan_baku')
            ->join('bahan_baku', 'menu_bahan_baku.bahan_baku_id', '=', 'bahan_baku.id')
            ->where('menu_bahan_baku.menu_id', $menuId)
            ->sum(DB::raw('bahan_baku.harga_satuan * menu_bahan_baku.jumlah'));

        // Tambahkan biaya tetap sebesar 10.000
        $totalHarga += 10000;

        // Debugging: Tampilkan query dan hasilnya
        \Log::info('Total Harga untuk Menu ID ' . $menuId . ': ' . $totalHarga);

        // Perbarui harga menu di database
        Menu::where('id', $menuId)->update(['harga' => $totalHarga]);
    }

    public function update(MenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $filename = $menu->gambar;

        if ($request->hasFile('gambar')) {
            if ($menu->gambar) {
                Storage::disk('public')->delete('menu-image/' . $menu->gambar);
            }

            $image = $request->file('gambar');
            $filename = date('Y-m-d') . $image->getClientOriginalName();
            $path = 'menu-image/' . $filename;
            Storage::disk('public')->put($path, file_get_contents($image));
        }

        $userId = auth()->id() ?? 1;

        $menu->update([
            'nama_makanan' => $request->nama_makanan,
            'user_id' => $userId,
            'stok' => 0, // Stok diatur ke 0, akan dihitung otomatis
            'kategori' => $request->kategori,
            'gambar' => $filename,
            'deskripsi' => $request->deskripsi,
        ]);

        // Panggil fungsi updateMenuPrice setelah menu diperbarui
        $this->updateMenuPrice($menu->id);

        // Perbarui stok menu berdasarkan bahan baku
        $this->perbaruiStokMenu($menu->id);

        $user = auth()->user();

        if ($user && $user->role == 'superuser') {
            return redirect()->route('superuser.menu')->with('success', 'Menu Berhasil Diperbarui');
        } elseif ($user && $user->role == 'karyawan') {
            return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Diperbarui');
        } else {
            return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Diperbarui');
        }
    }

    public function destroy(Request $request, $id)
    {
        $menu = Menu::find($id);

        $menu->delete();

        $user = auth()->user();
        if ($user->role == 'superuser') {
            return redirect()->route('superuser.menu')
                ->with('success', 'Menu Berhasil Dihapus');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.menu')
                ->with('success', 'Menu Berhasil Dihapus');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Hitung stok menu berdasarkan bahan baku yang tersedia.
     */
    public function hitungStokMenu($menuId)
{
    // Ambil data bahan baku yang dibutuhkan untuk menu
    $bahanBakuMenu = \DB::table('menu_bahan_baku')
        ->join('bahan_baku', 'menu_bahan_baku.bahan_baku_id', '=', 'bahan_baku.id')
        ->where('menu_bahan_baku.menu_id', $menuId)
        ->select('bahan_baku.stok', 'menu_bahan_baku.jumlah')
        ->get();

    if ($bahanBakuMenu->isEmpty()) {
        return 0; // Jika tidak ada bahan baku, stok menu = 0
    }

    $stokMenu = null; // Gunakan null dulu untuk menghindari kesalahan

    foreach ($bahanBakuMenu as $bahan) {
        if ($bahan->jumlah > 0) {
            $stokTersedia = floor($bahan->stok / $bahan->jumlah);
            $stokMenu = is_null($stokMenu) ? $stokTersedia : min($stokMenu, $stokTersedia);
        }
    }

    return $stokMenu ?? 0; // Jika tidak ada bahan baku valid, return 0
}


    /**
     * Perbarui stok menu di database berdasarkan bahan baku yang tersedia.
     */
    public function perbaruiStokMenu($menuId)
    {
        $stokMenu = $this->hitungStokMenu($menuId);
        Menu::where('id', $menuId)->update(['stok' => $stokMenu]);
    }
}