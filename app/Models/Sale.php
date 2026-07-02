<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'company_id',
        'client_id',
        'warehouse_id',
        'terminal_id',
        'user_id',
        'serie',
        'number',
        'status',
        'date',
        'subtotal',
        'discount',
        'total'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
