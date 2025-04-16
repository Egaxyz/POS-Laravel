<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Tambahkan Logging

class MemberController extends Controller
{
    public function index()
    {
        $member = Member::orderBy('nama_member', 'asc')->paginate(5);
        $user = auth()->user();
        
        if ($user->role == 'karyawan') {
            return view('Karyawan/Member/index', compact('member'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'nama_member' => 'required|string|max:50',
                'kontak' => 'required|numeric',
                'email' => 'required|email|max:30',
                'alamat' => 'required|string|max:200',
                'status' => 'required|in:aktif,nonaktif',
            ]);

            $user = User::create(
                [
                    'nama' => $request->nama_member,
                    'password' => bcrypt($request->password),
                    'role' => 'member',
                    'status' => $request->status,
                    'no_hp' => $request->kontak,
                ]
            );

            $member = Member::create([
                'user_id' => $user->id,
                'nama_member' => $request->nama_member,
                'kontak' => $request->kontak,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'tanggal_bergabung' => now(),
                'status' => $request->status,
            ]);

            DB::commit();

            // Catat aktivitas ke dalam log
            Log::info("User " . auth()->user()->name . " menambahkan member baru: " . $request->nama_member);

            return redirect()->route('karyawan.member')->with('success', 'Data Member dan User Berhasil Ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'nama_member' => 'required|string|max:50',
                'kontak' => 'required|numeric',
                'email' => 'required|email|max:30',
                'alamat' => 'required|string|max:200',
                'status' => 'required|in:aktif,nonaktif',
            ]);

            $member = Member::findOrFail($id);
            $user = User::findOrFail($member->user_id);

            $user->update([
                'nama' => $request->nama_member,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'role' => 'member',
                'status' => $request->status,
                'no_hp' => $request->kontak,
            ]);

            $member->update([
                'nama_member' => $request->nama_member,
                'kontak' => $request->kontak,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'status' => $request->status,
            ]);

            DB::commit();

            // Catat aktivitas ke dalam log
            Log::info("User " . auth()->user()->name . " mengupdate member: " . $request->nama_member);

            return redirect()->route('karyawan.member')->with('success', 'Data Member dan User Berhasil Diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        if (!$member) {
            return redirect()->route('karyawan.member')->with('error', 'Member tidak ditemukan');
        }

        // Catat nama sebelum dihapus
        $namaMember = $member->nama_member;

        $member->delete();

        // Catat aktivitas ke dalam log
        Log::info("User " . auth()->user()->name . " menghapus member: " . $namaMember);

        return redirect()->route('karyawan.member')->with('success', 'Data Member Berhasil Dihapus');
    }
}