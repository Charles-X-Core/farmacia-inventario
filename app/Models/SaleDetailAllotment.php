<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetailAllotment extends Model
{
    protected $fillable = [
        'sale_detail_id',
        'allotment_id',
        'quantity'
    ];

    public function saleDetail()
    {
        return $this->belongsTo(SaleDetail::class);
    }

    public function allotment()
    {
        return $this->belongsTo(Allotment::class);
    }
}
