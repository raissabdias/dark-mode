<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\News;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Exibir os comentários aprovados para uma notícia específica
     */
    public function index($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        return response()->json($news->comments);
    }

    /**
     * Armazenar um novo comentário para uma notícia específica
     */
    public function store(Request $request, $slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|string|min:3|max:1000',
        ]);

        $comment = $news->comments()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'content' => $validated['content'],
            'is_approved' => true
        ]);

        return response()->json($comment, 201);
    }
}