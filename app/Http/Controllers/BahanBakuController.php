<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Menu;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
{
    $supplier = Supplier::all();
    $menu = Menu::all();
    $bahan = BahanBaku::with('menu')->orderBy('nama', 'asc')->paginate(4);
    $user = auth()->user();
        
        if ($user->role == 'superuser') {
            return view('superuser/Bahan_Baku/index', compact('supplier', 'bahan', 'menu'));
        } elseif($user->role == 'karyawan') {
            return view('karyawan/Bahan_Baku/index', compact('supplier', 'bahan', 'menu'));
        }else {
            abort(403, 'Unauthorized action.');
        }
}

    public function store(Request $request){

        $validated = $request->validate([
            'supplier_id'=>'required|',
            'nama' => 'required',
            'stok' => 'required',
            'satuan' => 'required',
            'harga_satuan' =>'required',

        ]);
        $bahan = BahanBaku::create($validated);

        $user = auth()->user();
        
        if ($user->role == 'superuser') {
        return redirect()->route('superuser.bahan-baku')
                ->with('success', 'Bahan Baku Berhasil Ditambah');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.bahan-baku')
                ->with('success', 'Bahan Baku Berhasil Ditambah');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'supplier_id' => 'required|',
        'nama' => 'required|',
        'stok' => 'required|',
        'satuan' => 'required|',
        'harga_satuan' => 'required|',
    ]);

    $bahan = BahanBaku::find($id);
    $bahan->update($request->all());

        $user = auth()->user();
    if ($user->role == 'superuser') {
        return redirect()->route('superuser.bahan-baku')
                ->with('success', 'Bahan Baku Berhasil Diperbarui');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.bahan-baku')
                ->with('success', 'Bahan Baku Berhasil Diperbarui');
       } else {
            abort(403, 'Unauthorized action.');
        }
}


    public function destroy(Request $request, $id){
        $bahan = BahanBaku::find($id);

      $bahan -> delete();

        $user = auth()->user();
if ($user->role == 'superuser') {
        return redirect()->route('superuser.bahan-baku')
                ->with('success', 'Bahan Baku Berhasil Dihapus');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.bahan-baku')
                ->with('success', 'Bahan Baku Berhasil Dihapus');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
}