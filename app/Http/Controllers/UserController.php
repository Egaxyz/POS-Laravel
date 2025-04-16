<?php

namespace App\Http\Controllers;

use App\Imports\PegawaiImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
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
    public function showImportForm()
{
    return view('user.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new PegawaiImport, $request->file('file'));

    return back()->with('success', 'Data Pegawai berhasil diimpor!');
}
}