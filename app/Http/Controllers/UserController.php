<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $user = User::all();

        return view('Manager/User/index', ['user'=>$user]);
    }
    public function store(Request $request){
        $validated = $request->validate([
            'nama' => 'required',
            'password' => 'required',
            'no_hp' => 'required',
            'status' => 'required',

        ]);
        $validated['password'] = bcrypt($validated['password']);
        $data = User::create($validated);
        
        $user = auth()->user();
        if ($user->role == 'superuser') {
        return redirect()->route('superuser.user')
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
        if ($user->role == 'superuser') {
            return redirect()->route('superuser.user')
            ->with('success', 'User Berhasil Diperbarui');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.user')
            ->with('success', 'User Berhasil Diperbarui');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
    
    public function destroy(Request $request, $id){
        $data = User::find($id);
        $user = auth()->user();

      $data -> delete();

       if ($user->role == 'superuser') {
        return redirect()->route('superuser.user')
                ->with('success', 'User Berhasil Dihapus');
        } elseif ($user->role == 'manager') {
            return redirect()->route('manager.user')
                ->with('success', 'User Berhasil Dihapus');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
}