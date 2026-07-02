<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Allotment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'movement_id',
        'warehouse_id',
        'code',
        'expiration_date',
        'flag_closed',
        'status',
    ];

    protected $casts = [
        'flag_closed' => 'boolean',
        'status' => 'boolean',
        'expiration_date' => 'date',
    ];

    protected $appends = [
        'day_expiration_left',
    ];

    public function getDayExpirationLeftAttribute()
    {
        if (!$this->expiration_date) {
            return null;
        }
        $now = Carbon::now();
        return $now->diffInDays($this->expiration_date, false);
    }

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function allotmentDetails()
    {
        return $this->hasMany(AllotmentDetail::class);
    }
}
