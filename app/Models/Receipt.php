<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'sale_id',
        'receipt_number',
        'issued_at',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
