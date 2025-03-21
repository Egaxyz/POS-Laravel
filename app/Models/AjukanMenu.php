<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AjukanMenu extends Model
{
    protected $table = 'ajukan_menu';
    public $timestamps = false;
    public $fillable = [
        'user_id',
        'nama_makanan',
        'kategori',
        'harga',
        'stok',
        'deskripsi',
        'tanggal',
        'status'
    ];
        public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}