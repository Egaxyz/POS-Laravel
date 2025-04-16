<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    public $table = 'supplier';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
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