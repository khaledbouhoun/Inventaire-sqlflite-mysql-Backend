<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestqr extends Model
{
    protected $table = 'gestqr';

    // Composite key → disable incrementing + no single primary key
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;
    protected $keyType = null;

    protected $fillable = [
        'gqr_no',
        'gqr_lemp_no',
        'gqr_usr_no',
        'gqr_prd_no',
        'gqr_date',
    ];

    protected $casts = [
        'gqr_no' => 'integer',        // local counter
        'gqr_lemp_no' => 'integer',   // lemplacement id
        'gqr_usr_no' => 'integer',    // user id
        'gqr_prd_no' => 'string',     // product id as string
        'gqr_date' => 'datetime',     // date
    ];

    // Append derived name attributes when serializing to JSON
    protected $appends = [
        'gqr_lemp_nom',
        'gqr_usr_nom',
        'gqr_prd_nom',
        'gqr_prd_qr',

    ];


    // ---------------------------
    // Relations
    // ---------------------------

    // Lemplacement relation
    public function lemplacement()
    {
        return $this->belongsTo(Lemplacement::class, 'gqr_lemp_no', 'lemp_no');
    }

    // User relation
    public function user()
    {
        return $this->belongsTo(User::class, 'gqr_usr_no', 'usr_no');
    }

    // Product relation
    public function product()
    {
        return $this->belongsTo(Product::class, 'gqr_prd_no', 'prd_no');
    }

    // Accessors to expose related names as top-level attributes
    public function getGqrLempNomAttribute()
    {
        return $this->lemplacement ? $this->lemplacement->lemp_nom : null;
    }

    public function getGqrUsrNomAttribute()
    {
        return $this->user ? $this->user->usr_nom : null;
    }

    public function getGqrPrdNomAttribute()
    {
        return $this->product ? $this->product->prd_nom : null;
    }

    public function getGqrPrdQrAttribute()
    {
        return $this->product ? $this->product->prd_qr : null;
    }
}
