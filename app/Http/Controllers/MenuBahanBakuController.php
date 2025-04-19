<?php
namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\MenuBahanBaku;
use DB;
use Illuminate\Http\Request;
use App\Models\Menu; // Pastikan untuk mengimpor model Menu

class MenuBahanBakuController extends Controller
{
    public function index(){
        $bb = BahanBaku::all();
        $list = MenuBahanBaku::all();
        $data = Menu::with(['menuBahanBaku.bahanBaku']) // relasi hingga bahanBaku
                ->paginate(4);
        return view('Karyawan/Menu_Bahan_Baku/index', compact('data', 'list', 'bb'));
    }
    /**
 * @brief Menyimpan bahan baku yang ditambahkan ke menu dan memperbarui harga menu.
 * 
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
    public function store(Request $request)
    {
        // Decode data dari input tersembunyi
        $data = json_decode($request->bahan, true);

        // Merge data ke dalam request
        $request->merge([
            'bahan_baku_id' => $data['bahan_baku_id'] ?? [],
            'jumlah' => $data['jumlah'] ?? [],
        ]);

        // Validasi data
        $request->validate([
            'menu_id' => 'required|exists:menu,id',
            'bahan_baku_id' => 'required|array',
            'bahan_baku_id.*' => 'exists:bahan_baku,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'numeric|min:1',
        ]);

        // Simpan data ke database
        $menu_id = $request->menu_id;
        $bahan_baku_ids = $request->bahan_baku_id;
        $jumlahs = $request->jumlah;

        foreach ($bahan_baku_ids as $index => $bahan_id) {
            DB::table('menu_bahan_baku')->insert([
                'menu_id' => $menu_id,
                'bahan_baku_id' => $bahan_id,
                'jumlah' => $jumlahs[$index],
            ]);
        }

        // Panggil fungsi untuk memperbarui harga menu
        (new MenuController)->updateMenuPrice($menu_id);

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan ke menu.');
    }
    public function destroy(Request $request, $id){
        $data = MenuBahanBaku::findOrFail($id);

      $data -> delete();
        $user = auth()->user();
        if ($user->role == 'admin') {
        return redirect()->route('admin.menu-bahan-baku')
                ->with('success', 'Menu Bahan Baku Berhasil Dihapus');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.menu-bahan-baku')
                ->with('success', 'Menu Bahan Baku Berhasil Dihapus');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
}