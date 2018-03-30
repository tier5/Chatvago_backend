<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    /**
     * @var array fillable
     */
    protected $fillable=[
        'email','token'
    ];

    /**
     * @var bool timestamps
     */
    public $timestamps = false;
}
