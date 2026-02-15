<?php

use App\Models\Ad;
use App\Models\Event;
use App\Models\News;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CommentController;
use App\Models\Category;

Route::get('/news', function () {
    return News::where('is_active', true)
        ->with(['category'])
        ->orderBy('published_at', 'desc')
        ->take(9)
        ->get();
});

Route::get('/news/featured', function (Request $request) {
    $limit = $request->input('limit', 15);
    return \App\Models\News::where('is_active', true)
        ->where('is_featured', true)
        ->with(['category'])
        ->orderBy('published_at', 'desc')
        ->take($limit)
        ->get();
});

Route::get('/news/{slug}', function ($slug) {
    return News::where('slug', $slug)
        ->with(['category'])
        ->firstOrFail();
});

Route::get('/news-paginated', function (Request $request) {
    $query = News::with('category');
    if ($request->has('categories') && $request->categories) {
        $categoryIds = explode(',', $request->categories);
        
        $query->whereIn('category_id', $categoryIds);
    }
    
    if ($request->has('category_slug')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('slug', $request->category_slug);
        });
    }

    $query->where('is_active', true);
    $query->orderBy('published_at', 'desc');
    return $query->paginate(9);
});

Route::get('/events', function (Request $request) {
    $query = Event::query();
    if ($request->filled('year')) {
        $query->whereYear('event_date', $request->year);
    }

    if ($request->filled('month')) {
        $query->whereMonth('event_date', $request->month);
    }

    $query->where('is_active', true);
    $query->orderBy('event_date', 'asc');
    $perPage = $request->input('per_page', 10);

    return $query->paginate($perPage);
});

Route::get('/events/comming', function () {
    return Event::whereDate('event_date', '>=', now())
        ->where('is_active', true)
        ->orderBy('event_date', 'asc')
        ->take(10)
        ->get();
});

Route::get('/ads', function () {
    return Ad::where('is_active', true)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->get();
});

Route::get('/news/search/query', function (Request $request) {
    $query = $request->input('q');
    
    if (empty($query) || strlen($query) < 2) {
        return [];
    }
    
    $searchTerm = strtolower($query);
    
    return News::where('is_active', true)
        ->where(function($q) use ($searchTerm) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(excerpt) LIKE ?', ["%{$searchTerm}%"]);
        })
        ->with(['category'])
        ->orderBy('published_at', 'desc')
        ->take(8)
        ->get();
});

Route::get('/news/{slug}/comments', [CommentController::class, 'index']);

Route::post('/news/{slug}/comments', [CommentController::class, 'store']);

Route::get('/categories', function () {
    return Category::orderBy('name')->get();
});