<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
       public $table = 'menu';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'nama_makanan',
        'harga',
        'stok',
        'deskripsi',
        'gambar'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}