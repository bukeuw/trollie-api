<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'title',
        'description',
        'list_id',
        'due_date',
    ];

    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class);
    }
}
