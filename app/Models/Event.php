<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'event_date', 'location', 'ticket_url', 'image', 'is_active'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url', 'date_formatted', 'day', 'month'];

    public function getImageUrlAttribute()
    {
        return $this->image ? url("storage/{$this->image}") : null;
    }

    public function getDateFormattedAttribute()
    {
        return $this->event_date ? $this->event_date->format('d/m/Y H:i') : null;
    }

    public function getDayAttribute()
    {
        return $this->event_date ? $this->event_date->format('d') : null;
    }

    public function getMonthAttribute()
    {
        return $this->event_date ? $this->event_date->format('M') : null;
    }
}