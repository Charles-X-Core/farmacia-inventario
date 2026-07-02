<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type_document_id',
        'document',
    ];

    public function typeDocument()
    {
        return $this->belongsTo(TypeDocument::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function warehouses()
    {
        return $this->hasManyThrough(Warehouse::class, Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
