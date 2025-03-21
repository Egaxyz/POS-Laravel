<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    public $fillable = [
        'user_id',
        'nama_member',
        'kontak',
        'email',
        'tanggal_lahir',
        'alamat',
        'tanggal_bergabung',
        'status'
    ];
    public $timestamps = false;

    protected $table = 'member';
}