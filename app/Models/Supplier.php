<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    
    public $table = 'supplier';
    public $timestamps = false;
    protected $fillable = [
        'nama_perusahaan',
        'kontak',
        'alamat',
        'email',
        'status'
    ];
    public function bahanBaku()
{
    return $this->hasMany(BahanBaku::class, 'supplier_id', 'id');
}
}