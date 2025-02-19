<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index(Request $request){
        $bahan = BahanBaku::all();

         return response()->json([
            'success' => true,
            'message' => 'Data Bahan Berhasil Diambil',
            'data' => $bahan,
        ], 200);
    }
    public function store(Request $request){
        
        $validated = $request->validate([
            'supplier_id'=>'required',
            'nama' => 'required',
            'stok' => 'required',
            'satuan' => 'required',
            'harga_satuan' =>'required',

        ]);
        $supplier = Supplier::all();
        $bahan = BahanBaku::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Data Bahan Berhasil Ditambah',
            'data' => $bahan, $supplier
        ], 200);
    }
    public function update(Request $request, $id){
        $supplier = Supplier::find($id);

      $supplier -> nama_perusahaan = $request->nama_perusahaan;
      $supplier -> kontak = $request->kontak;
      $supplier -> alamat = $request->alamat;
      $supplier -> email = $request->email;
      $supplier -> status = $request->status;
      $supplier->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Supplier Berhasil Diperbarui',
            'data' => $supplier,
        ], 200);
    }

    public function destroy(Request $request, $id){
        $supplier = Supplier::find($id);

      $supplier -> delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Supplier Berhasil Dihapus',
        ], 200);
    }
}