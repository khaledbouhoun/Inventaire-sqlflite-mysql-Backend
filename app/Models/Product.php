<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'prd_no';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;


    protected $fillable = [
        'prd_no',
        'prd_nom',
        'prd_qr',
    ];

    protected $casts = [
        'prd_no' => 'string',
        'prd_nom' => 'string',

    ];
}
