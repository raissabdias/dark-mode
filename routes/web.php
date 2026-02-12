<?php

use App\Models\News;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/**
 * Redirect temporário de rotas antigas utéis para SEO e manutenção de links
 */
Route::get('/pgnews/{slug}', function ($slug) {
    $slugClean = strtolower(trim(str_replace('.htm', '', $slug)));

    $redirects = [
        '0033-tribulation-interview' => 'noticia/tribulation-volta-ao-brasil-e-com-baterista-da-crypta',
        '0028-psb-backtobr'          => 'noticia/pet-shop-boys-confirma-show-no-brasil-da-dreamworld-tour',
    ];

    if (array_key_exists($slugClean, $redirects)) {
        return redirect()->to(url($redirects[$slugClean]), 301);
    }

    return redirect('/', 301);
})->where('slug', '.*');

/**
 * Rota catch-all para SPA (Single Page Application)
 */
Route::get('/{any?}', function ($any = null) {
    $meta = [
        'title' => 'Dark Mode | Portal Underground',
        'description' => 'Acompanhe os melhores shows e as últimas novidades da cena underground.',
        'image'       => asset('images/og.jpg'),
        'url'         => url('/'),
    ];

    
    if (Str::startsWith($any, 'noticia/')) {
        $slug = Str::after($any, 'noticia/');
        $news = News::where('slug', $slug)->first(); 
        
        if ($news) {
            $meta['title'] = $news->title . ' | Dark Mode';
            $meta['description'] = Str::limit(strip_tags($news->excerpt ?? $news->content), 160);
            $meta['image'] = $news->image_url ?? $meta['image'];
        }
    }
    
    if (Str::startsWith($any, 'evento/')) {
        $id = Str::after($any, 'evento/');
        $event = Event::find($id);
        
        if ($event) {
            $meta['title'] = $event->title . ' | Agenda Dark Mode';
            $meta['description'] = 'Evento em ' . $event->location . ' no dia ' . date('d/m', strtotime($event->date));
            $meta['image'] = $event->image_url ?? $meta['image'];
        }
    }

    return view('welcome', compact('meta'));
})->where('any', '^(?!api|admin|importar-agora|limpar-cache).*');

/**
 * Rota para limpar todos os caches do Laravel (configurações, rotas, views, etc.) para garantir que as mudanças sejam refletidas imediatamente
 */
Route::get('/limpar-cache', function() {
    Artisan::call('optimize:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return "<h1>Todos os caches foram limpos com sucesso! 🦇</h1>";
});