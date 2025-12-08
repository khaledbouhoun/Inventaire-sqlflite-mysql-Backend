<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invontaie extends Model
{
    use HasFactory;

    protected $table = 'invontaie';
    protected $primaryKey = 'inv_no';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    protected $fillable = [
        'inv_lemp_no',
        'inv_pntg_no',
        'inv_usr_no',
        'inv_prd_no',
        'inv_exp',
        'inv_qte',
        'inv_date'
    ];

    protected $casts = [
        'inv_qte' => 'float',
        'inv_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'inv_usr_no', 'usr_no');
    }

    public function pointage()
    {
        return $this->belongsTo(Pointage::class, 'inv_pntg_no', 'pntg_no');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'inv_prd_no', 'prd_no');
    }

    public function lemplacement()
    {
        return $this->belongsTo(Lemplacement::class, 'inv_lemp_no', 'lemp_no');
    }

    // artibute
    public function getInvDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

}
