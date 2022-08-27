<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    //relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //relasi ke category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
