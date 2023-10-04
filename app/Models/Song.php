<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'artist',
        'idSpotify',
        'previewUrl',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }
}
