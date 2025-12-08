<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    use HasFactory;

    protected $table = 'pointage';
    protected $primaryKey = 'pntg_no';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    protected $fillable = [
        'pntg_no',
        'pntg_nom'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'usr_pntg', 'pntg_no');
    }

    public function invontaies()
    {
        return $this->hasMany(Invontaie::class, 'inv_pntg_no', 'pntg_no');
    }
}