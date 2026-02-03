<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'author',
        'excerpt',
        'content',
        'image',
        'published_at',
        'is_active'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url', 'date_formatted'];

    public function getImageUrlAttribute()
    {
        return $this->image ? url("storage/{$this->image}") : null;
    }

    public function getDateFormattedAttribute()
    {
        return $this->published_at ? $this->published_at->format('d/m/y') : null;
    }
}