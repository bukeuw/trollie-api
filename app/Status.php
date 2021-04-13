<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        'title',
    ];

    public function cards()
    {
        return $this->belongsToMany(Card::class);
    }
}
