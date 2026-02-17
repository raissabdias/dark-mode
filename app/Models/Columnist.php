<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['avatar_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Storage::disk('supabase')->url($this->avatar) : null;
    }
}
