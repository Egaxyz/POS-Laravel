<?php

namespace App\Http\Controllers;

use App\Imports\SupplierImport;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index(Request $request){

        $supplier = Supplier::orderBy('nama_perusahaan')->paginate(5);
        $user = auth()->user();
        
        if ($user->role == 'admin') {
            return view('admin/Supplier/index', compact('supplier'));
        } elseif($user->role == 'manager') {
            return view('Manager/Supplier/index', compact('supplier'));
        }else {
            abort(403, 'Unauthorized action.');
        }
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
        $user = auth()->user();
        if ($user->role == 'admin') {
        return redirect()->route('admin.supplier')
                ->with('success', 'Supplier Berhasil Ditambah');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.supplier')
                ->with('success', 'Supplier Berhasil Ditambah');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
    public function update(Request $request, $id){
        $supplier = Supplier::find($id);

      $supplier -> nama_perusahaan = $request->nama_perusahaan;
      $supplier -> kontak = $request->kontak;
      $supplier -> alamat = $request->alamat;
      $supplier -> email = $request->email;
      $supplier -> status = $request->status;
      $supplier->save();


      $user = auth()->user();
        if ($user->role == 'admin') {
        return redirect()->route('admin.supplier')
                ->with('success', 'Supplier Berhasil Diperbarui');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.supplier')
                ->with('success', 'Supplier Berhasil Diperbarui');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function destroy(Request $request, $id){
        $supplier = Supplier::find($id);

      $supplier -> delete();
    $user = auth()->user();
        if ($user->role == 'admin') {
        return redirect()->route('admin.supplier')
                ->with('success', 'Supplier Berhasil Dihapus');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.supplier')
                ->with('success', 'Supplier Berhasil Dihapus');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
    public function showImportForm()
{
    return view('supplier.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new SupplierImport, $request->file('file'));

    return back()->with('success', 'Data Supplier berhasil diimpor!');
}
}