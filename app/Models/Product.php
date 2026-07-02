<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'barcode',
        'price',
        'price_cost',
        'is_inventoriable',
        'is_price_cost',
        'is_sale',
        'type_management_id',
        'status',
    ];

    protected $casts = [
        'is_inventoriable' => 'boolean',
        'is_price_cost' => 'boolean',
        'is_sale' => 'boolean',
        'status' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function typeManagement()
    {
        return $this->belongsTo(TypeManagement::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_products')
                    ->withPivot('stock_available')
                    ->withTimestamps();
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_products')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function movementDetails()
    {
        return $this->hasMany(MovementDetail::class);
    }

    public function allotmentDetails()
    {
        return $this->hasMany(AllotmentDetail::class);
    }

}
