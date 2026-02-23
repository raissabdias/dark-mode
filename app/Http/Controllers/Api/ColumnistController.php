<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Columnist;
use App\Models\News;

class ColumnistController extends Controller
{
    /**
     * Exibir os detalhes de um colunista específico e suas notícias associadas
     */
    public function show($slug)
    {
        $columnist = Columnist::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $news = News::where('columnist_id', $columnist->id)
            ->with(['category', 'columnist'])
            ->where('is_active', true)
            ->latest('published_at')
            ->paginate(9);

        return response()->json([
            'columnist' => $columnist,
            'news' => $news
        ]);
    }
}