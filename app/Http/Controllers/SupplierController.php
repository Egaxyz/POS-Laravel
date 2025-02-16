<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request){
        $supplier = Supplier::all();

         return response()->json([
            'success' => true,
            'message' => 'Data Supplier Berhasil Diambil',
            'data' => $supplier,
        ], 200);
    }
    public function store(Request $request){
        $validated = $request->validate([
            'nama_perusahaan' => 'required',
            'kontak' => 'required',
            'alamat' => 'required',
            'email' =>'required',
            'status'=>'required',

        ]);
        $supplier = Supplier::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Data Supplier Berhasil Ditambah',
            'data' => $supplier,
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