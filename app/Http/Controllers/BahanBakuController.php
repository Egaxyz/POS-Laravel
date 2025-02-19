<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
{
    $supplier = Supplier::all();
    $bahan = BahanBaku::all();
    return view('Bahan_Baku.index', compact('supplier', 'bahan'));
}

    public function store(Request $request){

        $validated = $request->validate([
            'supplier_id'=>'required|',
            'nama' => 'required',
            'stok' => 'required',
            'satuan' => 'required',
            'harga_satuan' =>'required',

        ]);
        $supplier = Supplier::all();
        $bahan = BahanBaku::create($validated);
        
        return redirect()->route('Bahan-Baku', ['bahan'=>$bahan, 'supplier'=>$supplier])->with('success', 'Data Bahan Baku Berhasil Ditambahkan');
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

    return redirect()->route('Bahan-Baku')->with('success', 'Data Bahan Baku Berhasil Diperbarui');
}


    public function destroy(Request $request, $id){
        $bahan = BahanBaku::find($id);

      $bahan -> delete();

      return redirect()->route('Bahan-Baku', ['bahan'=>$bahan])->with('success', 'Data Bahan Baku Berhasil Dihapus');
    }
}