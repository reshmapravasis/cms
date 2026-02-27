@extends('layouts.app')

@section('content')
    <article class="max-w-4xl mx-auto px-6 py-20">
        <header class="mb-12 text-center">
            <div class="flex justify-center items-center gap-4 text-sm text-gray-500 mb-6">
                <span class="flex items-center uppercase tracking-widest font-bold text-[10px] text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Blog Post</span>
                <span>•</span>
                <span>{{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}</span>
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-8">
                {{ $post->title }}
            </h1>
            
            @if($post->excerpt)
                <p class="text-xl text-gray-600 font-medium leading-relaxed max-w-2xl mx-auto">
                    {{ $post->excerpt }}
                </p>
            @endif
        </header>

        @if($post->featured_image)
            <div class="mb-16 rounded-3xl overflow-hidden shadow-2xl">
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-auto">
            </div>
        @endif

        <div class="prose prose-lg md:prose-xl prose-blue max-w-none prose-img:rounded-3xl prose-img:shadow-xl">
            {!! $post->content !!}
        </div>
        
        <footer class="mt-20 pt-10 border-t border-gray-100 flex justify-between items-center">
            <a href="{{ route('blog.index') }}" class="group inline-flex items-center font-bold text-gray-900 hover:text-blue-600 transition">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Blog
            </a>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-400">Share:</span>
                <div class="flex gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="p-2 bg-gray-50 rounded-full hover:bg-blue-600 hover:text-white transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-8.74h-2.94v-3.403h2.94v-2.511c0-2.91 1.777-4.496 4.375-4.496 1.244 0 2.315.093 2.626.134v3.045h-1.802c-1.412 0-1.686.671-1.686 1.656v2.172h3.371l-.439 3.403h-2.932v8.74h6.141c.731 0 1.325-.593 1.325-1.324v-21.351c0-.732-.594-1.325-1.325-1.325z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="p-2 bg-gray-50 rounded-full hover:bg-blue-400 hover:text-white transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                </div>
            </div>
        </footer>
    </article>
@endsection
