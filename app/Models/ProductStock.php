<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'sku',
        'quantity',
        'last_synced_at',
    ];

    public $timestamps = true;
}
