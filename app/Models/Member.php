<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'nama_member',
        'kontak',
        'email',
        'alamat',
        'tanggal_bergabung',
        'status'
    ];
    public $timestamps = false;

    protected $table = 'member';
}