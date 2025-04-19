<?php

namespace App\Http\Controllers;

use App\Exports\SupplierExport;
use App\Imports\SupplierImport;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
 /**
 * @brief Menampilkan daftar supplier berdasarkan role pengguna.
 * 
 * Method ini digunakan untuk menampilkan daftar supplier dengan pengaturan 
 * pagination 5 item per halaman. Tampilan disesuaikan dengan role pengguna
 * yang sedang login.
 *
 * @param Request $request
 * @return \Illuminate\View\View
 */
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
    /**
 * @brief Menyimpan data supplier baru.
 * 
 * Method ini digunakan untuk menambah data supplier baru ke dalam sistem 
 * dengan validasi input yang diterima dari request.
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
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
/**
 * @brief Memperbarui data supplier yang sudah ada.
 * 
 * Method ini digunakan untuk memperbarui data supplier yang sudah ada berdasarkan 
 * ID supplier yang diberikan.
 *
 * @param Request $request
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse
 */
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
/**
 * @brief Menghapus data supplier yang sudah ada.
 * 
 * Method ini digunakan untuk menghapus data supplier berdasarkan ID yang 
 * diberikan oleh pengguna.
 *
 * @param Request $request
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse
 */
    public function destroy(Request $request, $id){
        $supplier = Supplier::findOrFail($id);

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
    /**
 * @brief Menampilkan form impor data supplier.
 * 
 * Method ini menampilkan form untuk mengimpor data supplier melalui file 
 * eksternal dengan format xlsx, xls, atau csv.
 *
 * @return \Illuminate\View\View
 */
    public function showImportForm()
{
    return view('supplier.import');
}
/**
 * @brief Mengimpor data supplier dari file berupa excel.
 * 
 * Method ini digunakan untuk mengimpor data supplier dari file yang diupload 
 * pengguna akan diarahkan kembali dengan pesan sukses.
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new SupplierImport, $request->file('file'));

    return back()->with('success', 'Data Supplier berhasil diimpor!');
}
/**
 * Mengekspor data supplier ke dalam format Excel.
 * 
 * @brief Fungsi ini digunakan untuk mengekspor data supplier ke dalam file Excel (.xlsx).
 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
 */
  public function exportExcel()
    {
        return Excel::download(new SupplierExport, 'Data-Supplier.xlsx');
    }
}