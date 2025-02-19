<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $user = User::all();

        return view('User/index', ['user'=>$user]);
    }
    public function store(Request $request){
        $validated = $request->validate([
            'nama' => 'required',
            'password' => 'required',
            'no_hp' => 'required',
            'status' => 'required',

        ]);
        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        return redirect()->route('User', ['user' => $user])
                ->with('success', 'User Berhasil Ditambahkan');
    }
    public function update(Request $request, $id){
        $user = User::find($id);

      $user -> nama = $request->nama;
      $user -> password = bcrypt($request->password);
      $user -> no_hp = $request->no_hp;
      $user -> role = $request->role;
      $user -> status = $request->status;
      $user->save();

        return redirect()->route('User', ['user' => $user])
                ->with('success', 'User Berhasil Diperbarui');
    }

    public function destroy(Request $request, $id){
        $user = User::find($id);

      $user -> delete();

        return redirect()->route('User', ['user' => $user])
                ->with('success', 'User Berhasil Dihapus');
    }
}