@php
    // Group consecutive services+documents blocks into pairs for 2-col layout
    $blocks = $content ?? [];
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

@foreach($grouped as $block)
    <div class="block-container relative">
        @switch($block['type'])
            @case('marquee')
                @php
                    $effectClass = [
                        'none' => '',
                        'shadow' => 'marquee-shadow',
                        'glow' => 'marquee-glow',
                        'outline' => 'marquee-outline'
                    ][$block['data']['text_effect'] ?? 'none'] ?? '';
                    
                    $textClasses = implode(' ', [
                        $block['data']['font_size'] ?? 'text-base',
                        $block['data']['font_weight'] ?? 'font-medium',
                        'tracking-wide',
                        $effectClass
                    ]);
                @endphp
                <div class="w-full overflow-hidden whitespace-nowrap py-4 shadow-inner relative z-10" 
                     style="background-color: {{ $block['data']['bg_color'] ?? '#1e40af' }}; color: {{ $block['data']['text_color'] ?? '#ffffff' }};">
                    <div class="flex items-center w-max min-w-full {{ $block['data']['speed'] ?? 'animate-marquee-normal' }} hover:pause-marquee">
                        {{-- Render items twice for infinite loop --}}
                        @for($i = 0; $i < 2; $i++)
                            <div class="flex items-center flex-shrink-0">
                                @foreach($block['data']['items'] as $item)
                                    <div class="flex items-center px-10">
                                        @if(!empty($item['link']))
                                            <a href="{{ $item['link'] }}" class="hover:underline {{ $textClasses }}">
                                                {{ $item['text'] }}
                                            </a>
                                        @else
                                            <span class="{{ $textClasses }}">{{ $item['text'] }}</span>
                                        @endif
                                        <span class="mx-8 opacity-40 text-2xl">{{ $block['data']['separator'] ?? '•' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('hero')
                <section class="relative min-h-[350px] md:h-[450px] flex items-center justify-center bg-blue-900 overflow-hidden">
                    @if(isset($block['data']['background_image']))
                        <img src="{{ Storage::url($block['data']['background_image']) }}" 
                             class="absolute inset-0 w-full h-full object-cover opacity-60" 
                             alt="{{ $block['data']['heading'] ?? 'Hero Image' }}">
                    @endif
                    <div class="relative z-10 text-center px-6 md:px-12 py-12 md:py-20 max-w-5xl mx-auto">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 sm:mb-6 drop-shadow-md leading-tight" style="color: {{ $block['data']['heading_color'] ?? '#ffffff' }}">
                            {{ $block['data']['heading'] ?? '' }}
                        </h1>
                        <p class="text-base sm:text-lg md:text-xl font-light leading-relaxed drop-shadow-sm max-w-3xl mx-auto" style="color: {{ $block['data']['subheading_color'] ?? '#dbeafe' }}">
                            {{ $block['data']['subheading'] ?? '' }}
                        </p>
                    </div>
                </section>
                @break

             @case('rich_text')
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 prose prose-sm sm:prose-base md:prose-lg lg:prose-xl prose-blue max-w-none prose-img:rounded-xl prose-img:shadow-lg leading-relaxed md:leading-loose" style="color: {{ $block['data']['text_color'] ?? '#111827' }}">
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
                                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">{{ $block['data']['heading'] }}</h2>
                                <div class="h-1.5 w-16 bg-blue-600 mx-auto rounded-full"></div>
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

            @case('split_content')
                <section id="{{ $block['data']['anchor_id'] ?? '' }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                    <div class="flex flex-col @if($block['data']['image_position'] == 'left') md:flex-row-reverse @else md:flex-row @endif items-center gap-10 md:gap-16">
                        <div class="flex-grow">
                            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">{{ $block['data']['heading'] ?? '' }}</h2>
                            <div class="h-1.5 w-16 bg-blue-600 rounded-full mb-8"></div>
                            <div class="prose prose-sm md:prose-base prose-blue max-w-none prose-p:my-2 prose-headings:my-2" style="color: {{ $block['data']['text_color'] ?? '#374151' }}">
                                {!! $block['data']['content'] ?? '' !!}
                            </div>
                        </div>
                        <div class="w-full md:w-auto md:min-w-[200px] flex justify-center">
                            @if(!empty($block['data']['image']))
                                <div class="relative group" style="width: {{ $block['data']['image_width'] ?? '250' }}px; max-width: 100%;">
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
                @endphp
                <section id="{{ $block['data']['anchor_id'] ?? '' }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                    <div class="mb-10 text-center md:text-left">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">{{ $block['data']['heading'] ?? 'Our Services' }}</h2>
                        <div class="h-1.5 w-16 bg-blue-600 rounded-full mb-8 mx-auto md:mx-0"></div>
                        @if(!empty($block['data']['description']))
                            <div class="prose prose-sm md:prose-base prose-blue text-gray-600 max-w-3xl leading-relaxed">
                                {!! $block['data']['description'] !!}
                            </div>
                        @endif
                    </div>
                    <div class="{{ $showSidebar ? 'flex flex-col lg:flex-row gap-10 items-start' : '' }}">
                        <div class="{{ $showSidebar ? 'flex-1 min-w-0' : 'w-full' }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 {{ $colClass }} gap-6 md:gap-8 items-stretch">
                                @foreach($items as $item)
                                    <div class="group p-6 md:p-8 bg-white border border-gray-100 rounded-2xl md:rounded-3xl shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center flex flex-col items-center border-b-4 border-b-transparent hover:border-b-blue-500">
                                        @if(!empty($item['icon']))
                                            <div class="mb-6 flex items-center justify-center p-3 rounded-2xl bg-blue-50 group-hover:bg-blue-100 transition-colors duration-500">
                                                <img src="{{ Storage::url($item['icon']) }}"
                                                     class="w-full h-auto object-contain max-w-[120px]"
                                                     style="max-height: 120px;"
                                                     alt="{{ $item['title'] ?? 'Service' }}">
                                            </div>
                                        @endif
                                        @if(!empty($item['title']))
                                            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-3 leading-tight group-hover:text-blue-600 transition-colors">{{ $item['title'] }}</h3>
                                        @endif
                                        @if(!empty($item['description']))
                                            <div class="text-sm text-gray-600 leading-relaxed prose prose-sm prose-blue max-w-none prose-p:my-1 text-center">
                                                {!! $item['description'] !!}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
                @break

            @case('video_gallery')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                    @if(!empty($block['data']['heading']))
                        <div class="text-center mb-12">
                            <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">{{ $block['data']['heading'] }}</h2>
                            <div class="h-1.5 w-16 bg-blue-600 mx-auto rounded-full"></div>
                        </div>
                    @endif
                    @php
                        $cols = $block['data']['columns'] ?? '2';
                        $gridClass = [
                            '1' => 'grid-cols-1',
                            '2' => 'grid-cols-1 md:grid-cols-2',
                            '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
                        ][$cols] ?? 'grid-cols-1 md:grid-cols-2';
                    @endphp
                    <div class="grid {{ $gridClass }} gap-8">
                        @foreach($block['data']['videos'] ?? [] as $video)
                            <div class="space-y-4">
                                <div class="relative aspect-video rounded-3xl overflow-hidden shadow-lg bg-black group border border-gray-100">
                                    @if(($video['type'] ?? 'url') === 'url' && !empty($video['url']))
                                        @php
                                            $videoId = '';
                                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['url'], $match)) {
                                                $videoId = $match[1];
                                            }
                                        @endphp
                                        @if($videoId)
                                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen></iframe>
                                        @else
                                            <div class="flex items-center justify-center h-full text-white text-sm p-4 text-center">
                                                Invalid video URL: {{ $video['url'] }}
                                            </div>
                                        @endif
                                    @elseif(($video['type'] ?? 'url') === 'file' && !empty($video['file']))
                                        <video class="w-full h-full object-cover" controls>
                                            <source src="{{ Storage::url($video['file']) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <div class="flex items-center justify-center h-full text-white text-sm p-4 text-center">
                                            No video provided
                                        </div>
                                    @endif
                                </div>
                                @if(!empty($video['title']))
                                    <p class="text-lg font-bold text-gray-900 sm:text-center px-2">{{ $video['title'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
                @break

            @case('testimonials')
                <section class="py-24 bg-gray-900 overflow-hidden">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center mb-16">
                            <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight" style="color: {{ $block['data']['heading_color'] ?? '#ffffff' }}">{{ $block['data']['heading'] ?? 'What Our Clients Say' }}</h2>
                            <div class="h-1.5 w-16 bg-blue-500 mx-auto rounded-full"></div>
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
                <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 bg-white rounded-[2rem] md:rounded-[3rem] shadow-2xl shadow-blue-900/5 mt-12 mb-12 border border-blue-50">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ $block['data']['heading'] ?? 'Contact Us' }}</h2>
                        <div class="h-1.5 w-16 bg-blue-600 mx-auto rounded-full mb-6"></div>
                        <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ $block['data']['subheading'] ?? 'We would love to hear from you. Fill out the form below and we will get back to you soon.' }}</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-8 p-6 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 text-emerald-800 animate-in fade-in slide-in-from-top duration-500">
                            <div class="p-2 bg-emerald-500 rounded-full text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="font-semibold">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('inquiry.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Full Name *</label>
                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="John Doe"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Email Address *</label>
                            <input type="email" name="email" required value="{{ old('email') }}" placeholder="john@example.com"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('phone') border-red-500 @enderror">
                            @error('phone') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="How can we help?"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('subject') border-red-500 @enderror">
                            @error('subject') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">Message *</label>
                            <textarea name="message" required rows="5" placeholder="Tell us more about your inquiry..."
                                      class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none resize-none @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                            @error('message') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 pt-4">
                            <button type="submit" class="admin-btn w-full md:w-auto px-10 py-5 text-white font-black rounded-xl md:rounded-2xl transition-all duration-300 shadow-xl shadow-blue-500/30 hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                                Send Message
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
                                </div>
                                <p class="text-gray-500 font-bold uppercase tracking-[0.2em] text-xs md:text-sm">{{ $item['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
                @break

            @case('info_cards')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                        @foreach($block['data']['items'] as $item)
                            <div class="p-8 sm:p-12 md:p-16 rounded-3xl md:rounded-[2.5rem] shadow-xl text-center flex flex-col items-center justify-center transform hover:-translate-y-2 transition-all duration-700 overflow-hidden relative group min-h-[300px]" 
                                 style="background-color: {{ $item['bg_color'] ?? '#001a72' }}; color: {{ $item['text_color'] ?? '#ffffff' }};">
                                <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-6 md:mb-8 uppercase tracking-widest leading-tight decoration-white/30">{{ $item['title'] }}</h2>
                                <p class="text-base md:text-lg lg:text-xl leading-relaxed opacity-90 font-medium tracking-tight max-w-2xl">{{ $item['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
                @break

            @case('blog_feed')
                @php
                    $limit = $block['data']['limit'] ?? 3;
                    $colsCount = $block['data']['columns'] ?? 3;
                    $gridClasses = ($colsCount == 2) ? 'md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-3';
                    $latestPosts = \App\Models\Page::where('type', 'post')
                        ->where('is_published', true)
                        ->orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get();
                @endphp
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                    @if(!empty($block['data']['heading']))
                        <div class="text-center mb-16">
                            <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">{{ $block['data']['heading'] }}</h2>
                            @if(!empty($block['data']['subheading']))
                                <p class="text-gray-500 max-w-2xl mx-auto text-lg">{{ $block['data']['subheading'] }}</p>
                            @endif
                            <div class="h-1.5 w-16 bg-blue-600 mx-auto rounded-full mt-6"></div>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 {{ $gridClasses }} gap-10">
                        @foreach($latestPosts as $post)
                            <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full border border-gray-100 group">
                                @if($post->featured_image)
                                    <div class="aspect-video relative overflow-hidden">
                                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    </div>
                                @endif
                                <div class="p-8 flex flex-col flex-grow">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4 line-clamp-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600 transition">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 mb-6 text-sm line-clamp-3 leading-relaxed">
                                        {{ $post->excerpt ?? '' }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
                @break

            @case('documents')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 standalone-documents">
                    @include('partials.document_sidebar', ['block' => $block])
                </section>
                @break

            @case('services_with_docs')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 flex flex-col lg:flex-row gap-10 md:gap-16">
                    <div class="flex-1">
                        @php 
                            $servicesBlock = $block['services'];
                            $items = $servicesBlock['data']['items'] ?? [];
                        @endphp
                        <div class="mb-10 text-center md:text-left">
                            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">{{ $servicesBlock['data']['heading'] ?? 'Our Services' }}</h2>
                            <div class="h-1.5 w-16 bg-blue-600 rounded-full mb-8 mx-auto md:mx-0"></div>
                            @if(!empty($servicesBlock['data']['description']))
                                <div class="prose prose-sm md:prose-base prose-blue text-gray-600 max-w-3xl leading-relaxed">
                                    {!! $servicesBlock['data']['description'] !!}
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-stretch">
                            @foreach($items as $item)
                                <div class="group p-6 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 flex flex-col items-center text-center">
                                    @if(!empty($item['icon']))
                                        <div class="mb-4 p-2 rounded-xl bg-blue-50 group-hover:bg-blue-100 transition-colors">
                                            <img src="{{ Storage::url($item['icon']) }}" class="w-12 h-12 object-contain" alt="{{ $item['title'] }}">
                                        </div>
                                    @endif
                                    <h3 class="text-base font-bold text-gray-900 mb-2">{{ $item['title'] }}</h3>
                                    <div class="text-xs text-gray-600 line-clamp-3">
                                        {!! $item['description'] !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="w-full lg:w-80 flex-shrink-0">
                        @include('partials.document_sidebar', ['block' => $block['docs']])
                    </div>
                </section>
                @break
        @endswitch
    </div>
@endforeach
