@php
    $siteName = \App\Models\Setting::get('site_name', 'Pravasis IT Solution');
    $siteLogo = \App\Models\Setting::get('logo');
    $pages = \App\Models\Page::where('is_published', true)->get();
    
    $navColor = \App\Models\Setting::get('nav_link_color', '#4b5563');
    $navHoverColor = \App\Models\Setting::get('nav_link_hover_color', '#2563eb');
    $navActiveColor = \App\Models\Setting::get('nav_link_active_color', '#2563eb');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($page) ? ($page->seo_title ?? $page->title) . ' - ' : '' }}{{ $siteName }}</title>
    <meta name="description" content="{{ isset($page) ? ($page->seo_description ?? $siteName) : $siteName }}">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .prose ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        .prose ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        .prose li { margin-bottom: 0.5rem !important; }
        /* Allow parent block color to be inherited by all children, but inline styles will still win */
        .prose :where(p, h1, h2, h3, h4, li, strong) { color: inherit; }

        :root {
            --nav-color: {{ $navColor }};
            --nav-hover: {{ $navHoverColor }};
            --nav-active: {{ $navActiveColor }};
        }

        .nav-link { color: var(--nav-color); }
        .nav-link:hover { color: var(--nav-hover); }
        .nav-link.active { color: var(--nav-active); border-bottom: 2px solid var(--nav-active); }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <!-- Top Bar -->
        <div class="bg-gray-900 text-white text-xs py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
                <div class="flex items-center space-x-6">
                    @if($email = \App\Models\Setting::get('email'))
                        <div class="flex items-center">
                            <svg class="h-3.5 w-3.5 mr-1.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:{{ $email }}" class="hover:text-blue-400 transition">{{ $email }}</a>
                        </div>
                    @endif
                    @if($phone = \App\Models\Setting::get('phone'))
                        <div class="flex items-center">
                            <svg class="h-3.5 w-3.5 mr-1.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:{{ $phone }}" class="hover:text-blue-400 transition">{{ $phone }}</a>
                        </div>
                    @endif
                </div>
                @if($workingHours = \App\Models\Setting::get('working_hours'))
                    <div class="flex items-center">
                        <svg class="h-3.5 w-3.5 mr-1.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $workingHours }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Navigation Bar -->
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex justify-between items-center">
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="flex items-center space-x-3">
                    @if($siteLogo)
                        <img src="{{ Storage::url($siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto">
                    @endif
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-500 bg-clip-text text-transparent">
                        {{ $siteName }}
                    </span>
                </a>
            </div>
            <div class="hidden md:flex space-x-8 items-center h-full">
                @foreach($pages as $p)
                    @php $isActive = request()->is($p->slug) || request()->is($p->slug . '/*') || (request()->is('/') && $p->slug == 'home'); @endphp
                    <a href="{{ route('page.show', $p->slug) }}" 
                       class="nav-link font-medium transition duration-150 py-2 {{ $isActive ? 'active' : '' }}">
                        {{ $p->title }}
                    </a>
                @endforeach
            </div>
            <div class="flex items-center space-x-4">
                 <a href="/admin" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">
                    Admin Panel
                </a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-100 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500">
            <div class="flex justify-center space-x-6 mb-8">
                @php $socials = ['facebook', 'twitter', 'instagram', 'linkedin']; @endphp
                @foreach($socials as $social)
                    @if($link = \App\Models\Setting::get($social))
                        <a href="{{ $link }}" class="hover:text-blue-600 transition" target="_blank">{{ ucfirst($social) }}</a>
                    @endif
                @endforeach
            </div>
            <p class="text-sm">
                &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved. Professional CMS Development.
            </p>
        </div>
    </footer>
</body>
</html>
