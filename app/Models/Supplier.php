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
}