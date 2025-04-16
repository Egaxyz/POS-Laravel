<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'status',
        'keterangan',
        'is_selesai',
        'created_at',
        'updated_at',
    ];
// App\Models\Absen.php
public function user()
{
    return $this->belongsTo(User::class)->withDefault([
        'nama' => 'Karyawan Tidak Ditemukan'
    ]);
}


}