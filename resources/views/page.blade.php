@extends('layouts.app')

@php
    // Group consecutive services+documents blocks into pairs for 2-col layout
    $blocks = $page->content;
    $grouped = [];
    $i = 0;
    while ($i < count($blocks)) {
        $current = $blocks[$i];
        if ($current['type'] === 'services' && isset($blocks[$i+1]) && $blocks[$i+1]['type'] === 'documents') {
            $grouped[] = ['type' => 'services_with_docs', 'services' => $current, 'docs' => $blocks[$i+1]];
            $i += 2;
        } else {
            $grouped[] = $current;
            $i++;
        }
    }
@endphp

@section('content')
<article>
        @if($page->content)
            @foreach($grouped as $block)
                <div class="block-container relative">
                    @switch($block['type'])
                        @case('hero')
                            <section class="relative h-[450px] flex items-center justify-center bg-blue-900 overflow-hidden">
                                @if(isset($block['data']['background_image']))
                                    <img src="{{ Storage::url($block['data']['background_image']) }}" 
                                         class="absolute inset-0 w-full h-full object-cover opacity-60" 
                                         alt="{{ $block['data']['heading'] ?? 'Hero Image' }}">
                                @endif
                                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 drop-shadow-md" style="color: {{ $block['data']['heading_color'] ?? '#ffffff' }}">
                                        {{ $block['data']['heading'] ?? '' }}
                                    </h1>
                                    <p class="{{ $block['data']['text_size'] ?? 'text-xl' }} font-light leading-relaxed drop-shadow-sm" style="color: {{ $block['data']['subheading_color'] ?? '#dbeafe' }}">
                                        {{ $block['data']['subheading'] ?? '' }}
                                    </p>
                            </section>
                            @break

                         @case('rich_text')
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 prose {{ $block['data']['text_size'] ?? 'prose-lg lg:prose-xl' }} prose-blue max-w-none prose-img:rounded-2xl prose-img:shadow-lg" style="color: {{ $block['data']['text_color'] ?? '#111827' }}">
                                {!! $block['data']['content'] !!}
                            </div>
                            @break

                        @case('image')
                            @if(!empty($block['data']['image']))
                                <figure class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col items-center">
                                    <img src="{{ Storage::url($block['data']['image']) }}" 
                                         style="width: {{ $block['data']['image_width'] ?? '800' }}px; max-width: 100%;"
                                         class="h-auto rounded-3xl shadow-2xl hover:shadow-blue-500/20 transition-shadow duration-500" 
                                         alt="{{ $block['data']['alt'] ?? '' }}">
                                    @if(!empty($block['data']['caption']))
                                        <figcaption class="mt-4 text-center text-gray-500 italic font-medium px-4">
                                            {{ $block['data']['caption'] }}
                                        </figcaption>
                                    @endif
                                </figure>
                            @endif
                            @break

                        @case('video')
                            @if(!empty($block['data']['url']))
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                                    <div class="relative aspect-video rounded-3xl overflow-hidden shadow-2xl bg-black group">
                                        @php
                                            $videoId = '';
                                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $block['data']['url'], $match)) {
                                                $videoId = $match[1];
                                            }
                                        @endphp
                                        @if($videoId)
                                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen></iframe>
                                        @else
                                            <div class="flex items-center justify-center h-full text-white text-lg font-medium">
                                                Video format not supported. URL: {{ $block['data']['url'] }}
                                            </div>
                                        @endif
                                        @if(isset($block['data']['title']))
                                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                                                <p class="text-white font-semibold">{{ $block['data']['title'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @break

                        @case('gallery')
                            @if(!empty($block['data']['images']))
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                                    @if(!empty($block['data']['heading']))
                                        <div class="text-center mb-12">
                                            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $block['data']['heading'] }}</h2>
                                            <div class="h-1 w-20 bg-blue-600 mx-auto rounded-full"></div>
                                        </div>
                                    @endif
                                    @php
                                        $cols = $block['data']['columns'] ?? '3';
                                        $gridClass = [
                                            '2' => 'sm:grid-cols-2',
                                            '3' => 'sm:grid-cols-2 lg:grid-cols-3',
                                            '4' => 'sm:grid-cols-2 lg:grid-cols-4',
                                        ][$cols] ?? 'sm:grid-cols-2 lg:grid-cols-3';
                                    @endphp
                                    <div class="grid grid-cols-1 {{ $gridClass }} gap-8">
                                        @php 
                                            $galleryImages = collect($block['data']['images'])->map(function($item) {
                                                return [
                                                    'src' => Storage::url(is_array($item) ? ($item['image'] ?? '') : $item),
                                                    'type' => 'image',
                                                    'title' => $item['label'] ?? ''
                                                ];
                                            })->values()->toArray();
                                        @endphp
                                        @foreach($block['data']['images'] as $index => $item)
                                            <div class="space-y-4">
                                                @php $imagePath = is_array($item) ? ($item['image'] ?? '') : $item; @endphp
                                                <div class="group relative aspect-square overflow-hidden rounded-2xl shadow-lg cursor-pointer bg-gray-100"
                                                     onclick='openLightbox({{ json_encode($galleryImages) }}, {{ $index }})'>
                                                    <img src="{{ Storage::url($imagePath) }}" 
                                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                                         alt="{{ $item['label'] ?? 'Gallery image' }}">
                                                    <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/20 transition-colors duration-500 flex items-center justify-center">
                                                        <svg class="h-10 w-10 text-white opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100 duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                @if(!empty($item['label']))
                                                    <p class="text-sm font-semibold text-gray-700 text-center">{{ $item['label'] }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @break

                        @case('video_gallery')
                            @if(!empty($block['data']['videos']))
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                                    @if(!empty($block['data']['heading']))
                                        <div class="text-center mb-12">
                                            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $block['data']['heading'] }}</h2>
                                            <div class="h-1 w-20 bg-blue-600 mx-auto rounded-full"></div>
                                        </div>
                                    @endif
                                    @php
                                        $cols = $block['data']['columns'] ?? '2';
                                        $gridClass = [
                                            '1' => 'grid-cols-1',
                                            '2' => 'grid-cols-1 lg:grid-cols-2',
                                            '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
                                        ][$cols] ?? 'grid-cols-1 lg:grid-cols-2';
                                    @endphp
                                    @php 
                                        $galleryVideos = collect($block['data']['videos'])->map(function($video) {
                                            $type = $video['type'] ?? 'url';
                                            $url = $video['url'] ?? '';
                                            $file = $video['file'] ?? '';
                                            $src = '';
                                            $lightType = '';
                                            if ($type === 'url') {
                                                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                                                    $src = "https://www.youtube.com/embed/" . $match[1] . "?autoplay=1";
                                                    $lightType = 'youtube';
                                                }
                                            } else {
                                                $src = Storage::url($file);
                                                $lightType = 'video';
                                            }
                                            return [
                                                'src' => $src,
                                                'type' => $lightType,
                                                'title' => $video['title'] ?? ''
                                            ];
                                        })->values()->toArray();
                                    @endphp
                                    <div class="grid {{ $gridClass }} gap-10">
                                        @foreach($block['data']['videos'] as $index => $video)
                                            @php
                                                $type = $video['type'] ?? 'url';
                                                $url = $video['url'] ?? '';
                                                $file = $video['file'] ?? '';
                                            @endphp
                                            @if(($type === 'url' && !empty($url)) || ($type === 'file' && !empty($file)))
                                                <div class="space-y-4">
                                                    <div class="relative aspect-video rounded-3xl overflow-hidden shadow-2xl bg-black group cursor-pointer">
                                                        @if($type === 'url')
                                                            @php
                                                                $videoId = '';
                                                                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/||.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                                                                    $videoId = $match[1];
                                                                }
                                                            @endphp
                                                            @if($videoId)
                                                                <div class="relative w-full h-full group" onclick='openLightbox({{ json_encode($galleryVideos) }}, {{ $index }})'>
                                                                    <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition duration-700" alt="">
                                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                                        <div class="w-20 h-20 bg-blue-600/90 rounded-full flex items-center justify-center shadow-2xl transform group-hover:scale-110 transition duration-500">
                                                                            <svg class="h-10 w-10 text-white fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="relative w-full h-full group" onclick='openLightbox({{ json_encode($galleryVideos) }}, {{ $index }})'>
                                                                <video class="w-full h-full object-cover">
                                                                    <source src="{{ Storage::url($file) }}" type="video/mp4">
                                                                </video>
                                                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition flex items-center justify-center">
                                                                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center transform group-hover:scale-110 transition duration-500">
                                                                        <svg class="h-10 w-10 text-white fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if(!empty($video['title']))
                                                        <h3 class="text-lg font-bold text-gray-800 text-center">{{ $video['title'] }}</h3>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @break
                        @case('split_content')
                            @php
                                $isAbout = $page->slug == 'about';
                            @endphp
                            <section id="{{ $block['data']['anchor_id'] ?? '' }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                                <div class="flex flex-col @if($block['data']['image_position'] == 'left') md:flex-row-reverse @else md:flex-row @endif items-center gap-10 {{ $isAbout ? 'lg:gap-16' : 'lg:gap-10' }}">
                                    <div class="{{ $isAbout ? 'w-full md:w-1/2' : 'flex-grow' }}">
                                        <h2 class="{{ $isAbout ? 'text-2xl md:text-3xl' : 'text-xl md:text-2xl' }} font-bold mb-3 leading-tight" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">{{ $block['data']['heading'] ?? '' }}</h2>
                                        <div class="prose {{ $isAbout ? 'prose-base' : 'prose-sm' }} {{ $block['data']['text_size'] ?? '' }} prose-blue max-w-none prose-p:my-1 prose-headings:my-2" style="color: {{ $block['data']['text_color'] ?? '#374151' }}">
                                            {!! $block['data']['content'] ?? '' !!}
                                        </div>
                                    </div>
                                    <div class="{{ $isAbout ? 'w-full md:w-1/2' : 'w-full md:w-auto md:min-w-[200px]' }} flex justify-center">
                                        @if(!empty($block['data']['image']))
                                            <div class="relative group" style="width: {{ $block['data']['image_width'] ?? ($isAbout ? '500' : '250') }}px; max-width: 100%;">
                                                <div class="absolute -inset-2 bg-gradient-to-r from-blue-600 to-teal-500 rounded-2xl opacity-10 group-hover:opacity-20 transition duration-500"></div>
                                                <img src="{{ Storage::url($block['data']['image']) }}" 
                                                     class="relative w-full h-auto rounded-2xl shadow-xl object-contain hover:scale-[1.02] transition duration-500" 
                                                     alt="{{ $block['data']['heading'] ?? '' }}">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </section>
                            @break

                        @case('services')
                            @php
                                $items = $block['data']['items'] ?? [];
                                $columns = $block['data']['columns'] ?? '3';
                                $showSidebar = !empty($block['data']['show_sidebar']);
                                $colClass = [
                                    '2' => 'lg:grid-cols-2',
                                    '3' => 'lg:grid-cols-3',
                                    '4' => 'lg:grid-cols-2 xl:grid-cols-4',
                                    '5' => 'lg:grid-cols-3 xl:grid-cols-5',
                                    '6' => 'lg:grid-cols-3 xl:grid-cols-6',
                                ][$columns] ?? 'lg:grid-cols-3';
                                $documents = $block['data']['documents'] ?? [];
                            @endphp
                            <section id="{{ $block['data']['anchor_id'] ?? '' }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

                                {{-- Section Header --}}
                                <div class="mb-8">
                                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1 leading-tight">{{ $block['data']['heading'] ?? 'Our Services' }}</h2>
                                    <div class="h-1 w-10 bg-blue-600 rounded-full mb-3"></div>
                                    @if(!empty($block['data']['description']))
                                        <div class="prose prose-sm prose-blue text-gray-600 max-w-none leading-tight prose-p:my-1 {{ $block['data']['text_size'] ?? '' }}">
                                            {!! $block['data']['description'] !!}
                                        </div>
                                    @endif
                                </div>

                                {{-- Two-column layout when sidebar enabled --}}
                                <div class="{{ $showSidebar ? 'flex flex-col lg:flex-row gap-8 items-start' : '' }}">

                                    {{-- Main Services Area --}}
                                    <div class="{{ $showSidebar ? 'flex-1 min-w-0' : 'w-full' }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 {{ $colClass }} gap-6 items-stretch">
                                            @foreach($items as $item)
                                                <div class="group p-6 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 text-center flex flex-col items-center">
                                                    @if(!empty($item['icon']))
                                                        <div class="mb-5 flex items-center justify-center">
                                                            <img src="{{ Storage::url($item['icon']) }}"
                                                                 class="w-full h-auto object-contain"
                                                                 style="max-width: {{ ($block['data']['image_width'] ?? 150) * 1.1 }}px; max-height: {{ ($block['data']['image_width'] ?? 150) * 1.1 }}px;"
                                                                 alt="{{ $item['title'] ?? 'Service' }}">
                                                        </div>
                                                    @endif
                                                    @if(!empty($item['title']))
                                                        <h3 class="text-base font-bold text-gray-900 mb-2 leading-tight">{{ $item['title'] }}</h3>
                                                    @endif
                                                    @if(!empty($item['description']))
                                                        <div class="text-[13px] text-gray-600 leading-tight prose prose-sm prose-blue max-w-none prose-p:my-0.5 mb-4 text-left">
                                                            {!! $item['description'] !!}
                                                        </div>
                                                    @endif
                                                    @if(!empty($item['link']))
                                                        <div class="mt-auto pt-4 w-full">
                                                            <a href="{{ $item['link'] }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center justify-center gap-1 group/btn">
                                                                Read More
                                                                <svg class="h-3 w-3 transform group-hover/btn:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        @if(!empty($block['data']['view_all_link']))
                                            <div class="mt-10 flex justify-center">
                                                <a href="{{ $block['data']['view_all_link'] }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                                                    {{ $block['data']['view_all_text'] ?? 'Explore All Services' }}
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Sticky Sidebar --}}
                                    @if($showSidebar)
                                        <div class="w-full lg:w-72 xl:w-80 flex-shrink-0">
                                            <div class="sticky top-24 flex flex-col gap-5">

                                                {{-- Documents / Resources Card --}}
                                                @if(!empty($documents))
                                                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                                                        <div class="flex items-center gap-2 mb-4">
                                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                                            </svg>
                                                            <h3 class="font-bold text-gray-800 text-base">{{ $block['data']['sidebar_title'] ?? 'Resources' }}</h3>
                                                        </div>
                                                        <div class="flex flex-col gap-2">
                                                            @foreach($documents as $doc)
                                                                @if(!empty($doc['file']))
                                                                    <a href="{{ Storage::url($doc['file']) }}"
                                                                       target="_blank"
                                                                       class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 transition group">
                                                                        <div class="flex-shrink-0">
                                                                            <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                                                                                <text x="5" y="17" font-size="5" fill="white" font-weight="bold">PDF</text>
                                                                            </svg>
                                                                        </div>
                                                                        <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700 transition leading-tight">
                                                                            {{ $doc['name'] ?? 'Document' }}
                                                                        </span>
                                                                        <svg class="h-3 w-3 text-gray-400 group-hover:text-blue-500 ml-auto flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                        </svg>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Contact / Help Card --}}
                                                @if(!empty($block['data']['contact_link']))
                                                    <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-blue-600 to-blue-700 p-5 text-white shadow-lg shadow-blue-600/30">
                                                        <h3 class="font-bold text-lg mb-1">{{ $block['data']['contact_title'] ?? 'Need Help?' }}</h3>
                                                        <p class="text-blue-100 text-sm mb-5 leading-relaxed">{{ $block['data']['contact_text'] ?? 'Contact our experts for customized IT solutions.' }}</p>
                                                        <a href="{{ $block['data']['contact_link'] }}"
                                                           class="block w-full bg-white text-blue-700 font-bold text-center py-2.5 rounded-xl hover:bg-blue-50 transition text-sm">
                                                            {{ $block['data']['contact_btn_text'] ?? 'Contact Us' }}
                                                        </a>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </section>
                            @break

                        @case('testimonials')
                            <section class="py-24 bg-gray-900 overflow-hidden">
                                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                    <div class="text-center mb-16">
                                        <h2 class="text-4xl font-bold text-white mb-4">{{ $block['data']['heading'] ?? 'What Our Clients Say' }}</h2>
                                        <div class="h-1.5 w-20 bg-blue-500 mx-auto rounded-full"></div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                        @foreach($block['data']['items'] as $item)
                                            <div class="p-8 bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl">
                                                <div class="text-gray-300 italic mb-8 prose prose-sm prose-invert max-w-none">
                                                    {!! $item['quote'] ?? '' !!}
                                                </div>
                                                <div class="flex items-center gap-4">
                                                    @if(!empty($item['avatar']))
                                                        <img src="{{ Storage::url($item['avatar']) }}" class="w-12 h-12 rounded-full object-cover border-2 border-blue-500" alt="{{ $item['name'] ?? 'Client' }}">
                                                    @endif
                                                    <div>
                                                        <h4 class="text-white font-bold">{{ $item['name'] ?? 'Client' }}</h4>
                                                        <p class="text-gray-500 text-sm">{{ $item['role'] ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                            @break

                        @case('contact_form')
                            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-white rounded-3xl shadow-xl mt-12 mb-12">
                                <div class="text-center mb-12">
                                    <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $block['data']['heading'] ?? 'Contact Us' }}</h2>
                                    <p class="text-gray-600">{{ $block['data']['subheading'] ?? 'We would love to hear from you.' }}</p>
                                </div>
                                
                                @if(session('success'))
                                    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-8 text-center font-medium">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('inquiry.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                                        <input type="text" name="subject" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                                        <textarea name="message" rows="5" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                                    </div>
                                    <div class="md:col-span-2 flex justify-center mt-6">
                                        <button type="submit" class="bg-blue-600 text-white font-bold py-4 px-12 rounded-xl hover:bg-blue-700 transform hover:scale-105 transition-all shadow-xl shadow-blue-600/20">
                                            Send Message
                                        </button>
                                    </div>
                                </form>
                            </section>
                            @break
                        @case('stats')
                            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-white shadow-inner">
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-12 text-center">
                                    @foreach($block['data']['items'] as $item)
                                        <div class="stats-item flex flex-col items-center">
                                            <div class="text-5xl md:text-6xl font-sans font-black text-[#001a72] mb-3 flex items-center justify-center">
                                                <span class="counter" data-target="{{ $item['number'] }}">0</span>
                                                @if(!empty($item['suffix']))
                                                    <span class="text-2xl md:text-4xl text-blue-600 ml-1 font-bold">{{ $item['suffix'] }}</span>
                                                @endif
                                            </div>
                                            <div class="h-1 w-10 bg-blue-600 mb-5"></div>
                                            <p class="text-gray-500 font-bold uppercase tracking-[0.2em] text-xs md:text-sm">{{ $item['label'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                            @break

                        @case('info_cards')
                            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    @foreach($block['data']['items'] as $item)
                                        <div class="p-12 md:p-16 rounded-[2.5rem] shadow-2xl text-center transform hover:-translate-y-2 transition-all duration-700 overflow-hidden relative group" 
                                             style="background-color: {{ $item['bg_color'] ?? '#001a72' }}; color: {{ $item['text_color'] ?? '#ffffff' }};">
                                            <div class="absolute -inset-24 bg-blue-400 opacity-0 group-hover:opacity-10 blur-3xl transition-opacity animate-pulse duration-1000"></div>
                                            
                                            <h2 class="text-3xl md:text-4xl font-extrabold mb-8 uppercase tracking-widest leading-tight decoration-white/30 decoration-4 underline-offset-8">{{ $item['title'] }}</h2>
                                            <p class="{{ $item['text_size'] ?? 'text-lg' }} md:{{ $item['text_size'] ?? 'text-xl' }} leading-relaxed opacity-95 font-medium tracking-tight">{{ $item['description'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                            @break

                        @case('documents')
                            @php $docs = $block['data'] ?? []; @endphp
                            @include('partials.documents-sidebar', ['data' => $docs])
                            @break

                        @case('services_with_docs')
@php
    $sb = $block['services'];
    $db = $block['docs'];
    $items = $sb['data']['items'] ?? [];
@endphp

<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">

    {{-- Section Heading --}}
    @if(!empty($sb['data']['heading']))
        <div class="mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                {{ $sb['data']['heading'] }}
            </h2>
            <div class="h-1 w-16 bg-blue-600 rounded-full"></div>
            @if(!empty($sb['data']['description']))
                <div class="prose prose-sm prose-blue text-gray-600 mt-3 max-w-none {{ $sb['data']['text_size'] ?? '' }}">
                    {!! $sb['data']['description'] !!}
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] xl:grid-cols-[1fr_320px] gap-10 items-start">

        {{-- LEFT: Service Cards --}}
        <div class="space-y-6">
            @foreach($items as $item)
                <div class="flex flex-col sm:flex-row gap-5 bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">

                    {{-- Image --}}
                    @if(!empty($item['icon']))
                        <div class="flex-shrink-0 flex items-start justify-center">
                            <img src="{{ Storage::url($item['icon']) }}"
                                 class="w-36 sm:w-44 h-auto rounded-xl object-contain"
                                 alt="{{ $item['title'] ?? '' }}">
                        </div>
                    @endif

                    {{-- Content --}}
                    <div class="flex flex-col flex-1">
                        @if(!empty($item['title']))
                            <h3 class="font-bold text-lg text-gray-900 mb-2">
                                {{ $item['title'] }}
                            </h3>
                        @endif

                        @if(!empty($item['description']))
                            <div class="text-sm text-gray-600 leading-relaxed prose prose-sm prose-blue max-w-none prose-p:my-1">
                                {!! $item['description'] !!}
                            </div>
                        @endif

                        @if(!empty($item['link']))
                            <div class="mt-4 pt-2 border-t border-gray-50">
                                <a href="{{ $item['link'] }}"
                                   class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                    Inquire Now
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        {{-- RIGHT: Sticky Sidebar --}}
        <div>
            <div class="sticky top-24 flex flex-col gap-5">
                @include('partials.documents-sidebar', ['data' => $db['data']])
            </div>
        </div>

    </div>

</section>
@break

                    @endswitch
                </div>
            @endforeach
        @else
            <div class="max-w-4xl mx-auto px-6 py-20 text-center">
                <h1 class="text-3xl font-bold mb-4">{{ $page->title }}</h1>
                <p class="text-gray-500">No content available for this page yet. Edit in the admin panel to add sections.</p>
            </div>
        @endif
    </article>
@endsection

{{-- Lightbox / Fullscreen Modal Structure --}}
<div id="lightbox" class="fixed inset-0 z-[1000] hidden bg-black/95 backdrop-blur-md transition-all duration-300 opacity-0 flex items-center justify-center p-4">
    {{-- Controls Container --}}
    <div class="absolute top-0 inset-x-0 p-6 flex justify-between items-center z-[1001]">
        <div class="flex items-center gap-4">
            <span id="lightbox-counter" class="text-white/70 text-sm font-medium bg-white/10 px-3 py-1 rounded-full backdrop-blur-sm"></span>
            <button onclick="toggleZoom()" id="zoom-btn" class="p-2 text-white/50 hover:text-white transition-colors bg-white/5 rounded-lg hover:bg-white/10">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
            </button>
        </div>
        <button onclick="closeLightbox()" class="p-2 text-white/50 hover:text-white transition-colors bg-white/5 rounded-lg hover:bg-white/10">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    {{-- Navigation Buttons --}}
    <button onclick="prevItem()" id="prev-btn" class="absolute left-6 p-4 text-white/30 hover:text-white transition-all transform hover:scale-110 z-[1001] bg-white/5 rounded-full backdrop-blur-sm hover:bg-white/20">
        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
    </button>
    <button onclick="nextItem()" id="next-btn" class="absolute right-6 p-4 text-white/30 hover:text-white transition-all transform hover:scale-110 z-[1001] bg-white/5 rounded-full backdrop-blur-sm hover:bg-white/20">
        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </button>
    
    <div class="w-full max-w-6xl h-full flex flex-col items-center justify-center pt-16 pb-12">
        <div id="lightbox-content" class="w-full h-full flex items-center justify-center overflow-hidden">
            {{-- Content injected via JS --}}
        </div>
        <div class="mt-6 flex flex-col items-center gap-2">
            <h3 id="lightbox-title" class="text-white text-xl font-bold tracking-wide"></h3>
            {{-- Scroll Line (Progress Bar) --}}
            <div class="w-48 h-1 bg-white/10 rounded-full overflow-hidden mt-2">
                <div id="lightbox-progress" class="h-full bg-blue-500 transition-all duration-300 w-0"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    #lightbox.active { display: flex; opacity: 1; }
    #lightbox-content img, #lightbox-content video, #lightbox-content iframe { 
        max-width: 100%; 
        max-height: 80vh; 
        object-fit: contain;
        border-radius: 0.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #lightbox-content.zoomed img {
        transform: scale(1.5);
        cursor: zoom-out;
    }
    #lightbox-content img {
        cursor: zoom-in;
    }
    .animate-in { animation: zoomIn 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes zoomIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
<script>
    let currentGallery = [];
    let currentIndex = 0;
    let isZoomed = false;

    function openLightbox(items, index) {
        currentGallery = items;
        currentIndex = index;
        isZoomed = false;
        renderLightboxItem();
        
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('hidden');
        setTimeout(() => lightbox.classList.add('active'), 10);
        document.body.style.overflow = 'hidden';
    }

    function renderLightboxItem() {
        const item = currentGallery[currentIndex];
        const content = document.getElementById('lightbox-content');
        const titleEl = document.getElementById('lightbox-title');
        const counter = document.getElementById('lightbox-counter');
        const progress = document.getElementById('lightbox-progress');
        const zoomBtn = document.getElementById('zoom-btn');
        
        content.classList.remove('zoomed');
        isZoomed = false;
        content.innerHTML = '';
        titleEl.innerText = item.title || '';
        counter.innerText = `${currentIndex + 1} / ${currentGallery.length}`;
        progress.style.width = `${((currentIndex + 1) / currentGallery.length) * 100}%`;
        
        // Toggle zoom button visibility
        zoomBtn.style.display = item.type === 'image' ? 'block' : 'none';

        let el;
        if (item.type === 'image') {
            el = `<img src="${item.src}" class="animate-in" onclick="toggleZoom()" alt="">`;
        } else if (item.type === 'video') {
            el = `<video src="${item.src}" controls autoplay class="w-full animate-in"></video>`;
        } else if (item.type === 'youtube') {
            el = `<iframe src="${item.src}" class="w-full aspect-video animate-in" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`;
        }
        
        content.innerHTML = el;
        updateNavButtons();
    }

    function updateNavButtons() {
        document.getElementById('prev-btn').style.opacity = currentGallery.length <= 1 ? '0' : '1';
        document.getElementById('next-btn').style.opacity = currentGallery.length <= 1 ? '0' : '1';
        document.getElementById('prev-btn').style.pointerEvents = currentGallery.length <= 1 ? 'none' : 'auto';
        document.getElementById('next-btn').style.pointerEvents = currentGallery.length <= 1 ? 'none' : 'auto';
    }

    function nextItem() {
        currentIndex = (currentIndex + 1) % currentGallery.length;
        renderLightboxItem();
    }

    function prevItem() {
        currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
        renderLightboxItem();
    }

    function toggleZoom() {
        if (currentGallery[currentIndex].type !== 'image') return;
        isZoomed = !isZoomed;
        document.getElementById('lightbox-content').classList.toggle('zoomed', isZoomed);
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            lightbox.classList.add('hidden');
            document.getElementById('lightbox-content').innerHTML = '';
        }, 300);
    }

    // Keyboard controls
    document.addEventListener('keydown', (e) => {
        if (!document.getElementById('lightbox').classList.contains('active')) return;
        
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextItem();
        if (e.key === 'ArrowLeft') prevItem();
        if (e.key === '+' || e.key === '=') toggleZoom();
    });

    // Close on background click
    document.getElementById('lightbox').addEventListener('click', (e) => {
        if (e.target.id === 'lightbox' || e.target.id === 'lightbox-content') {
            closeLightbox();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.counter');
        const speed = 100; // Total duration factor

        const animateCounter = (counter) => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            
            // Adjust speed based on target size
            const duration = 1500; // Total duration in ms
            const frameRate = 1000 / 60; // 60 FPS
            const totalFrames = duration / frameRate;
            const increment = target / totalFrames;

            let currentCount = 0;
            
            const updateCount = () => {
                currentCount += increment;
                if (currentCount < target) {
                    counter.innerText = Math.ceil(currentCount);
                    requestAnimationFrame(updateCount);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    // Slight delay before start for better perception
                    setTimeout(() => {
                        animateCounter(entry.target);
                    }, 200);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    });
</script>
@endpush
