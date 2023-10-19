<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'token',
    ];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            $group->token = Str::random(10); // Génère un token aléatoire de 10 caractères lors de la création
        });
    }




    public function winner()
    {
        return $this->belongsTo(User::class, 'winner');
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }
}
