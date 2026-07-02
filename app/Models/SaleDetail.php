<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'movement_detail_id',
        'quantity',
        'price',
        'subtotal'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movementDetail()
    {
        return $this->belongsTo(MovementDetail::class);
    }

    public function allotments()
    {
        return $this->hasMany(SaleDetailAllotment::class);
    }
}
