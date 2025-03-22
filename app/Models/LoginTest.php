<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginTest extends Model
{
    protected $fillable = [
        'nama',
        'password',
        'status',
        'error_message'
    ];
}