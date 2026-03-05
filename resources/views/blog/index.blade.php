@extends('layouts.app')

@section('content')
    {{-- Render Content Blocks from Admin Panel --}}
    @if($page && $page->content)
        @include('partials.blocks_loop', ['content' => $page->content])
    @endif

    {{-- Default List (Only show if no blog_feed block was used) --}}
    @php
        $hasBlogFeedBlock = $page && $page->content ? collect($page->content)->contains('type', 'blog_feed') : false;
    @endphp

    @if(!$hasBlogFeedBlock)
        <div class="bg-gray-50 py-10">
            <div class="max-w-7xl mx-auto px-6">
                @if(!$page || !$page->content || count($page->content) === 0)
                    <div class="text-center mb-16">
                        @php
                            $titleSize = $page->title_size ?? 'text-4xl md:text-5xl';
                        @endphp
                        <h1 class="{{ $titleSize }} font-bold text-gray-900 mb-4">
                            {{ $page->title ?? 'Latest News & Updates' }}
                        </h1>
                        <div class="h-1.5 w-20 bg-blue-600 mx-auto rounded-full"></div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @forelse($posts as $post)
                        <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full border border-gray-100">
                            @if($post->featured_image)
                                <div class="aspect-video relative overflow-hidden">
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </div>
                            @else
                                <div class="aspect-video bg-gradient-to-br from-blue-600 to-teal-500 flex items-center justify-center">
                                    <span class="text-white font-bold text-xl">{{ $post->title[0] }}</span>
                                </div>
                            @endif
                            
                            <div class="p-8 flex flex-col flex-grow">
                                <div class="flex items-center gap-4 text-xs text-gray-400 mb-4">
                                    <span class="flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $post->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <h2 class="{{ $post->title_size ?? 'text-2xl' }} font-bold text-gray-900 mb-4 line-clamp-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600 transition">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                
                                <p class="text-gray-600 mb-8 line-clamp-3 leading-relaxed">
                                    @if($post->excerpt)
                                        {{ $post->excerpt }}
                                    @else
                                        @php
                                            $plainText = '';
                                            if (is_array($post->content)) {
                                                foreach ($post->content as $block) {
                                                    if ($block['type'] === 'rich_text' && !empty($block['data']['content'])) {
                                                        $plainText .= strip_tags($block['data']['content']) . ' ';
                                                    } elseif ($block['type'] === 'split_content' && !empty($block['data']['content'])) {
                                                        $plainText .= strip_tags($block['data']['content']) . ' ';
                                                    }
                                                }
                                            }
                                        @endphp
                                        {{ Str::limit($plainText, 150) }}
                                    @endif
                                </p>
                                
                                <div class="mt-auto">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-blue-600 font-bold hover:gap-2 transition-all duration-300">
                                        Read Full Story
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="bg-white p-12 rounded-3xl shadow-sm inline-block">
                                <svg class="w-16 h-16 text-gray-200 mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900">No blog posts found</h3>
                                <p class="text-gray-500 mt-2">We'll be posting something interesting soon. Stay tuned!</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-16">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection
