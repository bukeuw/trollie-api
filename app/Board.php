<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
        'title',
        'color',
    ];

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
