<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'author',
        'excerpt',
        'content',
        'image',
        'published_at',
        'is_active', 
        'is_featured'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = ['image_url', 'date_formatted'];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::disk('supabase')->url($this->image) : null;
    }

    public function getDateFormattedAttribute()
    {
        return $this->published_at ? $this->published_at->format('d/m/y') : null;
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
