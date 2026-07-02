<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementDetail extends Model
{
    protected $fillable = [
        'movement_id',
        'product_id',
        'quantity_in',
        'quantity_out',
        'quantity_now',
        'price_cost',
        'price_cost_now',
        'utilites',
    ];

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
