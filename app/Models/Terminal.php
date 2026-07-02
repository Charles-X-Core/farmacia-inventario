<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Terminal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'branch_id',
        'status',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function series()
    {
        return $this->hasMany(Series::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
