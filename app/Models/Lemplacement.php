<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lemplacement extends Model
{
    use HasFactory;

    protected $table = 'lemplacement';
    protected $primaryKey = 'lemp_no';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;


    protected $fillable = [
        'lemp_no',
        'lemp_nom'
    ];


    // Relations

    public function gestqrs()
    {
        return $this->hasMany(Gestqr::class, 'gqr_lemp_no', 'lemp_no');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'usr_lemp', 'lemp_no');
    }

    public function invontaies()
    {
        return $this->hasMany(Invontaie::class, 'inv_lemp_no', 'lemp_no');
    }
}
