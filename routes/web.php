<?php

use App\Models\News;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

/**
 * ROTA CATCH-ALL (Home, Notícias e Eventos com Meta Tags)
 * Esta rota captura a URL e injeta os metadados antes de carregar o Vue/React
 */
Route::get('/{any?}', function ($any = null) {
    
    // 1. Metadados Padrão (Home)
    $meta = [
        'title' => 'Dark Mode | Portal Underground',
        'description' => 'Acompanhe os melhores shows e as últimas novidades da cena underground.',
        'image' => asset('images/background.jpg'),
        'url' => url()->current(),
    ];

    // 2. Lógica para Notícias (Prefixo 'noticia/')
    if (Str::startsWith($any, 'noticia/')) {
        $slug = Str::after($any, 'noticia/');
        $news = News::where('slug', $slug)->first(); 
        
        if ($news) {
            $meta['title'] = $news->title . ' | Dark Mode';
            $meta['description'] = Str::limit(strip_tags($news->excerpt ?? $news->content), 160);
            // Certifique-se de que image_url retorna a URL completa do Supabase
            $meta['image'] = $news->image_url ?? $meta['image'];
        }
    }
    
    // 3. Lógica para Eventos (Prefixo 'evento/')
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
 * ROTA DE LIMPEZA DE CACHE
 * Útil após deploys ou alterações no .env e rotas
 */
Route::get('/limpar-cache', function() {
    Artisan::call('optimize:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return "<h1>Todos os caches foram limpos com sucesso! 🦇</h1>";
});