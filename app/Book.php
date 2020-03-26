<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isbn',
        'name',
        'title',
        'year',
    ];

    /**
     * Get the publisher who publishes the book
     *
     */
    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * Get the authors who wrote the book
     *
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }
}
