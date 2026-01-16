<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyMovement extends Model
{
    protected $fillable = [
        'supply_id',
        'type',
        'quantity',
        'reason',
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
    
}
