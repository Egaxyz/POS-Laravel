<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;
    public $table = 'menu';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'nama_makanan',
        'harga',
        'stok',
        'kategori',
        'deskripsi',
        'gambar'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
     public function bahanBaku() {
    return $this->belongsToMany(BahanBaku::class, 'menu_bahan_baku', 'menu_id', 'bahan_baku_id')
                ->withPivot('jumlah');
}
}