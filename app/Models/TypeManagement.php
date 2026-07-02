<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeManagement extends Model
{
    protected $table = 'type_management';

    protected $fillable = [
        'name',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
