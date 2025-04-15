<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;
use DB;
use Log;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::orderBy('nama_makanan', 'asc')->paginate(5);
        $user = auth()->user();


        foreach ($menu as $item) {
            $stokMenu = $this->hitungStokMenu($item->id);
            $item->stok = $stokMenu;

            $this->perbaruiStokMenu($item->id);
        }

        if ($user->role == 'admin') {
            return view('admin/Menu/index', compact('user', 'menu'));
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
            Log::error('Gagal mengupload gambar menu.');
            return back()->with('error', 'Gagal mengupload gambar. Pastikan file dipilih.');
        }

        $userId = auth()->id() ?? 1;

        $menu = Menu::create([
            'nama_makanan' => $request->nama_makanan,
            'user_id' => $userId,
            'harga' => 0,
            'stok' => 0,
            'kategori' => $request->kategori,
            'gambar' => $filename,
            'deskripsi' => $request->deskripsi,
        ]);

        Log::info('Menu baru berhasil ditambahkan: ' . $menu->nama_makanan . ' - ID: ' . $menu->id);

        $this->perbaruiStokMenu($menu->id);

        return redirect()->route(auth()->user()->role . '.menu')->with('success', 'Menu Berhasil Ditambah');
    }

    public function updateMenuPrice($menuId)
    {
        $totalHarga = DB::table('menu_bahan_baku')
            ->join('bahan_baku', 'menu_bahan_baku.bahan_baku_id', '=', 'bahan_baku.id')
            ->where('menu_bahan_baku.menu_id', $menuId)
            ->sum(DB::raw('bahan_baku.harga_satuan * menu_bahan_baku.jumlah'));

        $totalHarga += 10000;

        Log::info('Mengupdate harga menu ID: ' . $menuId . ' - Harga Baru: ' . $totalHarga);

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
            'stok' => 0,
            'kategori' => $request->kategori,
            'gambar' => $filename,
            'deskripsi' => $request->deskripsi,
        ]);

        Log::info('Menu ID: ' . $id . ' berhasil diperbarui.');

        $this->updateMenuPrice($menu->id);
        $this->perbaruiStokMenu($menu->id);

        return redirect()->route(auth()->user()->role . '.menu')->with('success', 'Menu Berhasil Diperbarui');
    }

    public function destroy(Request $request, $id)
    {

        $menu = Menu::find($id);
        if (!$menu) {
            Log::error('Gagal menghapus menu. Menu ID: ' . $id . ' tidak ditemukan.');
            return back()->with('error', 'Menu tidak ditemukan.');
        }

        $menu->delete();

        Log::info('Menu ID: ' . $id . ' berhasil dihapus.');

        return redirect()->route(auth()->user()->role . '.menu')->with('success', 'Menu Berhasil Dihapus');
    }

    public function hitungStokMenu($menuId)
    {

        $bahanBakuMenu = \DB::table('menu_bahan_baku')
            ->join('bahan_baku', 'menu_bahan_baku.bahan_baku_id', '=', 'bahan_baku.id')
            ->where('menu_bahan_baku.menu_id', $menuId)
            ->select('bahan_baku.stok', 'menu_bahan_baku.jumlah')
            ->get();

        if ($bahanBakuMenu->isEmpty()) {
            return 0;
        }

        $stokMenu = null;

        foreach ($bahanBakuMenu as $bahan) {
            if ($bahan->jumlah > 0) {
                $stokTersedia = floor($bahan->stok / $bahan->jumlah);
                $stokMenu = is_null($stokMenu) ? $stokTersedia : min($stokMenu, $stokTersedia);
            }
        }

        return $stokMenu ?? 0;
    }

    public function perbaruiStokMenu($menuId)
    {
        $stokMenu = $this->hitungStokMenu($menuId);
        Menu::where('id', $menuId)->update(['stok' => $stokMenu]);
    }
}