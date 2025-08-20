<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'caption',
        'image',
        'user_id'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    //relasi ke user
    public function user() {
        return $this->belongsTo(User::class);
    }

    //relasi ke like
    public function likes() {
        return $this->hasMany(Like::class);
    }

    //relasi ke comment
    public function comments() {
        return $this->hasMany(Comment::class);
    }
}
