<?php

// [Anggota 2] Daftar & detail artikel untuk pengunjung

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $articles = Article::published()
            ->when($request->q, fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('publik.artikel.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        abort_unless($article->published_at && $article->published_at->lte(now()), 404);

        $related = Article::published()->whereKeyNot($article->id)->latest('published_at')->take(3)->get();

        return view('publik.artikel.show', compact('article', 'related'));
    }
}
