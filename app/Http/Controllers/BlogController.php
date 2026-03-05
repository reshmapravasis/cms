<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'blog')->first();
        
        $posts = Page::where('type', 'post')
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('blog.index', compact('posts', 'page'));
    }

    public function show($slug)
    {
        $post = Page::where('type', 'post')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('page', ['page' => $post]);
    }
}
