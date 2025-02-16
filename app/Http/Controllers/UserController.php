<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $user = User::all();

         return response()->json([
            'success' => true,
            'message' => 'Data Pengguna Berhasil Diambil',
            'data' => $user,
        ], 200);
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
        return response()->json([
            'success' => true,
            'message' => 'Data User Berhasil Ditambah',
            'data' => $user,
        ], 200);
    }
    public function update(Request $request, $id){
        $user = User::find($id);

      $user -> nama = $request->nama;
      $user -> password = bcrypt($request->password);
      $user -> no_hp = $request->no_hp;
      $user -> status = $request->status;
      $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data User Berhasil Diperbarui',
            'data' => $user,
        ], 200);
    }

    public function destroy(Request $request, $id){
        $user = User::find($id);

      $user -> delete();

        return response()->json([
            'success' => true,
            'message' => 'Data User Berhasil Dihapus',
        ], 200);
    }
}