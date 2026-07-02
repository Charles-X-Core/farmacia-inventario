<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'company_id',
        'type_document_id',
        'document',
        'name',
        'last_name',
        'nit',
        'phone',
        'email',
        'address',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function typeDocument()
    {
        return $this->belongsTo(TypeDocument::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . ($this->last_name ?? ''));
    }
}
