<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllotmentDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'allotment_id',
        'product_id',
        'quantity',
        'quantity_dispatch',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function allotment()
    {
        return $this->belongsTo(Allotment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
