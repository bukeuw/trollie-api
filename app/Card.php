<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class);
    }
}
