<?php

use App\Models\News;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{any}', function ($any = '') {
    $meta = [
        'title' => 'Dark Mode | Portal Underground',
        'description' => 'Acompanhe os melhores shows e as últimas novidades da cena underground.',
        'image' => asset('images/background.jpg'),
        'url' => url()->current(),
    ];

    if (Str::startsWith($any, 'news/')) {
        $slug = explode('/', $any)[1] ?? null;
        
        if ($slug) {
            $news = News::where('slug', $slug)->first(); 
            
            if ($news) {
                $meta['title'] = $news->title . ' | Dark Mode';
                $meta['description'] = Str::limit(strip_tags($news->content ?? $news->body), 150); 
                $meta['image'] = $news->image_url ?: $meta['image'];
            }
        }
    }
    
    if (Str::startsWith($any, 'event/')) {
        $id = explode('/', $any)[1] ?? null;
        if ($id) {
            $event = Event::find($id);
            if ($event) {
                $meta['title'] = $event->title . ' | Dark Mode Agenda';
                $meta['description'] = 'Show imperdível! Dia ' . date('d/m/Y', strtotime($event->date)) . ' em ' . $event->location;
                $meta['image'] = $event->image_url ?: $meta['image'];
            }
        }
    }

    return view('welcome', compact('meta'));
})->where('any', '.*');