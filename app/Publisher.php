<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the books published by the publisher
     *
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
