<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function terminals()
    {
        return $this->hasMany(Terminal::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_branches');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_users');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'branch_products')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
