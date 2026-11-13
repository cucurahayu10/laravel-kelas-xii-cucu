<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $table = 'films';
    protected $fillable = ['title', 'sinopsis', 'year', 'poster', 'genre_id'];

    public function perans()
    {
        return $this->hasMany(Peran::class);
    }

    public function castMembers()
    {
        return $this->belongsToMany(Cast::class, 'perans', 'film_id', 'cast_id')
            ->withPivot('id', 'actor')
            ->withTimestamps();
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function kritiks()
    {
        return $this->hasMany(Kritik::class);
    }
}
