<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    //relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //relasi ke posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
