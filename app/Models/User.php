<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'usr_no';
    protected $keyType = 'integer';
    public $incrementing = true;

    // يجب أن يكون true لأن لديك remember_token + timestamps في الجدول
    public $timestamps = true;

    protected $fillable = [
        'usr_nom',
        'usr_pas',
        'usr_pntg',
        'usr_lemp',
    ];

    protected $hidden = [
        'usr_pas',
        'remember_token',
    ];

    // لجعل Laravel يفهم كلمة السر
    public function getAuthPassword()
    {
        return $this->usr_pas;
    }

    // Relations
    public function gestqrs()
    {
        return $this->hasMany(Gestqr::class, 'gqr_usr_no', 'usr_no');
    }

    public function lemplacement()
    {
        return $this->belongsTo(Lemplacement::class, 'usr_lemp', 'lemp_no');
    }

    public function getUserLempNomAttribute()
    {
        return $this->lemplacement ? $this->lemplacement->lemp_nom : null;
    }
}
