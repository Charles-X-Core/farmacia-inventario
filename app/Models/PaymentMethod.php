<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    //
    protected $fillable = [
        'name',
        'status'
    ];


    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
