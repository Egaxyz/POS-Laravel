<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Menu;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BahanBakuController extends Controller
{
    public function index()
    {

        $supplier = Supplier::all();
        $menu = Menu::all();
        $bahan = BahanBaku::with('menu')->orderBy('nama', 'asc')->paginate(4);
        $user = auth()->user();

        if ($user->role == 'admin') {
            return view('admin/Bahan_Baku/index', compact('supplier', 'bahan', 'menu'));
        } elseif ($user->role == 'karyawan') {
            return view('karyawan/Bahan_Baku/index', compact('supplier', 'bahan', 'menu'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'supplier_id' => 'required',
            'nama' => 'required',
            'stok' => 'required|numeric',
            'satuan' => 'required',
            'harga_satuan' => 'required|numeric',
        ]);

        BahanBaku::create($validated);

        Log::info('Bahan baku berhasil ditambahkan', ['user_id' => auth()->id(), 'nama' => $request->nama]);

        return $this->redirectToRole('Bahan Baku Berhasil Ditambah');
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'supplier_id' => 'required',
            'nama' => 'required',
            'stok' => 'required|numeric',
            'satuan' => 'required',
            'harga_satuan' => 'required|numeric',
        ]);

        $bahan = BahanBaku::findOrFail($id);
        $bahan->update($validated);

        Log::info('Bahan baku berhasil diperbarui', ['user_id' => auth()->id(), 'bahan_id' => $id]);

        return $this->redirectToRole('Bahan Baku Berhasil Diperbarui');
    }

    public function destroy($id)
    {

        $bahan = BahanBaku::findOrFail($id);
        $bahan->delete();

        Log::info('Bahan baku berhasil dihapus', ['user_id' => auth()->id(), 'bahan_id' => $id]);

        return $this->redirectToRole('Bahan Baku Berhasil Dihapus');
    }

    public function laporan(Request $request)
    {

        $bahan = BahanBaku::orderBy('nama', 'desc')->paginate(5);
        $user = auth()->user();

        if ($user->role == 'admin') {
            return view('admin.Laporan_Bahan_Baku.index', compact('bahan'));
        } elseif ($user->role == 'manager') {
            return view('manager.Laporan_Bahan_Baku.index', compact('bahan'));
        } else {
            abort(403, 'Anda tidak memiliki akses.');
        }
    }

    private function redirectToRole($message)
    {
        $user = auth()->user();
        if ($user->role == 'admin') {
            return redirect()->route('admin.bahan-baku')->with('success', $message);
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.bahan-baku')->with('success', $message);
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}