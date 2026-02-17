<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Columnist extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'bio', 
        'avatar', 
        'user_id', 
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
