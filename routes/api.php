<?php

use App\Models\Ad;
use App\Models\Event;
use App\Models\News;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/news', function () {
    return News::where('is_active', true)
        ->with(['category'])
        ->orderBy('published_at', 'desc')
        ->take(6)
        ->get();
});

Route::get('/news/featured', function () {
    return \App\Models\News::where('is_active', true)
        ->where('is_featured', true)
        ->with(['category'])
        ->orderBy('published_at', 'desc')
        ->take(3)
        ->get();
});

Route::get('/news/{slug}', function ($slug) {
    return News::where('slug', $slug)
        ->with(['author', 'category'])
        ->firstOrFail();
});

Route::get('/news-paginated', function (Request $request) {
    // Carrega a categoria junto
    $query = News::with('category')->latest();

    // Filtro por MÚLTIPLAS CATEGORIAS (IDs)
    // Espera algo como: ?categories=1,3,5
    if ($request->has('categories') && $request->categories) {
        // Transforma "1,3,5" em array [1, 3, 5]
        $categoryIds = explode(',', $request->categories);
        
        $query->whereIn('category_id', $categoryIds);
    }
    
    // Mantém a compatibilidade antiga (busca pelo slug único se necessário)
    // Útil se você clicar numa tag específica
    if ($request->has('category_slug')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('slug', $request->category_slug);
        });
    }

    return $query->paginate(9);
});

Route::get('/events', function () {
    return \App\Models\Event::query()
        ->where('event_date', '>=', now())
        ->orderBy('event_date', 'asc')
        ->paginate(10);
});

Route::get('/ads', function () {
    return Ad::where('is_active', true)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->get();
});

Route::get('/instalar-config-livewire', function () {
    \Illuminate\Support\Facades\Artisan::call('vendor:publish', [
        '--tag' => 'livewire:config',
        '--force' => true
    ]);
    
    return '<h1>Configuração do Livewire publicada</h1>';
});