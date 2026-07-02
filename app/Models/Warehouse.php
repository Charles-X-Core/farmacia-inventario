<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'branch_id',
    ];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'warehouse_branches');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'warehouses_users');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'warehouse_products')
                    ->withPivot('stock_available')
                    ->withTimestamps();
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function allotments()
    {
        return $this->hasMany(Allotment::class);
    }
}
