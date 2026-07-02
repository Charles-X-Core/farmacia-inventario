<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = [
        'warehouse_id',
        'terminal_id',
        'serie',
        'number',
        'employee_id',
        'movement_type_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function movementType()
    {
        return $this->belongsTo(MovementType::class);
    }

    public function movementDetails()
    {
        return $this->hasMany(MovementDetail::class);
    }

    public function allotments()
    {
        return $this->hasMany(Allotment::class);
    }
}
