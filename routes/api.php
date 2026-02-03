<?php

use App\Models\News;
use Illuminate\Support\Facades\Route;

Route::get('/news', function () {
    return News::where('is_active', true)
        ->orderBy('published_at', 'desc')
        ->take(6)
        ->get();
});

Route::get('/news/featured', function () {
    return \App\Models\News::where('is_active', true)
        ->where('is_featured', true)
        ->orderBy('published_at', 'desc')
        ->take(3)
        ->get();
});
