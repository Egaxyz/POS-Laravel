<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\UserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
 * @brief Menampilkan daftar pengguna berdasarkan role pengguna.
 * 
 * Method ini digunakan untuk menampilkan daftar pengguna dengan pengaturan 
 * pagination 5 item per halaman. Tampilan disesuaikan dengan role pengguna
 * yang sedang login.
 *
 * @param Request $request
 * @return \Illuminate\View\View
 */
    public function index(Request $request){
        $user = User::orderBy('nama')->paginate(5);

    $akun = auth()->user();
        
        if ($akun->role == 'admin') {
            return view('admin/User/index', compact('user' ));
        } elseif($akun->role == 'manager') {
            return view('Manager/User/index', compact('user'));
        }else {
            abort(403, 'Unauthorized action.');
        }
    }
    
/**
 * @brief Menyimpan data pengguna baru.
 * 
 * Method ini digunakan untuk menambah data pengguna baru ke dalam sistem 
 * dengan validasi input yang diterima dari request.
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
    public function store(Request $request){
        $validated = $request->validate([
            'nama' => 'required',
            'password' => 'required',
            'no_hp' => 'required',
            'status' => 'required',
            'role'=>'required'

        ]);
        $validated['password'] = bcrypt($validated['password']);
        $data = User::create($validated);
        
        $user = auth()->user();
        if ($user->role == 'admin') {
        return redirect()->route('admin.user')
                ->with('success', 'User Berhasil Ditambah');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.user')
                ->with('success', 'User Berhasil Ditambah');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
/**
 * @brief Memperbarui data pengguna yang sudah ada.
 * 
 * Method ini digunakan untuk memperbarui data pengguna yang sudah ada berdasarkan 
 * ID pengguna yang diberikan.
 *
 * @param Request $request
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse
 */
    public function update(Request $request, $id){
      $data = User::find($id);

      $data -> nama = $request->nama;
      $data -> password = bcrypt($request->password);
      $data -> no_hp = $request->no_hp;
      $data -> role = $request->role;
      $data -> status = $request->status;
      $data->save();

        $user = auth()->user();
        if ($user->role == 'admin') {
            return redirect()->route('admin.user')
            ->with('success', 'User Berhasil Diperbarui');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.user')
            ->with('success', 'User Berhasil Diperbarui');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
    /**
 * @brief Menghapus data pengguna yang sudah ada.
 * 
 * Method ini digunakan untuk menghapus data pengguna berdasarkan ID yang 
 * diberikan oleh pengguna.
 *
 * @param Request $request
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse
 */
    public function destroy(Request $request, $id){
        $data = User::findOrFail($id);
        $data->delete();
        $user = auth()->user();

      $data -> delete();

       if ($user->role == 'admin') {
        return redirect()->route('admin.user')
                ->with('success', 'User Berhasil Dihapus');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.user')
                ->with('success', 'User Berhasil Dihapus');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
/**
 * @brief Menampilkan form impor data pengguna.
 * 
 * Method ini menampilkan form untuk mengimpor data pengguna melalui file 
 * eksternal dengan format xlsx, xls, atau csv.
 *
 * @return \Illuminate\View\View
 */
    public function showImportForm()
{
    return view('user.import');
}
/**
 * @brief Mengimpor data pengguna dari file.
 * 
 * Method ini digunakan untuk mengimpor data pengguna dari file yang diupload 
 * oleh pengguna. File dapat berupa xlsx, xls, atau csv. Setelah impor berhasil, 
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

    Excel::import(new UserImport, $request->file('file'));

    return back()->with('success', 'Data Pegawai berhasil diimpor!');
}
/**
 * Mengekspor data user ke dalam format Excel.
 * 
 * @brief Fungsi ini digunakan untuk mengekspor data user ke dalam file Excel (.xlsx).
 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
 */
  public function exportExcel()
    {
        return Excel::download(new UserExport, 'Data-Pegawai.xlsx');
    }
}