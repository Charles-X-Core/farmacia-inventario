<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $fillable = [
        'terminal_id',
        'movement_type_id',
        'serie',
        'number',
        'status',
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function movementType()
    {
        return $this->belongsTo(MovementType::class);
    }
}
