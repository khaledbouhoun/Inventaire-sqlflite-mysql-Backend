<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'usr_id';
    public $timestamps = false; // since you don’t have created_at/updated_at

    protected $fillable = [
        'usr_nom',
        'usr_pas',
        'usr_pntg',
        'usr_dpot',
    ];

    protected $hidden = [
        'usr_pas',
        'remember_token',
    ];
}
