<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembelian extends Model
{
      public $table = 'pembelian';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'supplier_id',
        'tanggal_pembelian',
        'total_harga',
        'status_pembelian',
        'deskripsi',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function details()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_id');
    }

    // Relasi ke BahanBaku melalui DetailPembelian
    public function bahanBaku()
    {
        return $this->hasManyThrough(BahanBaku::class, DetailPembelian::class, 'pembelian_id', 'id', 'id', 'bahan_baku_id');
    }
    
}