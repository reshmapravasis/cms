@php
    $siteName = \App\Models\Setting::get('site_name', 'Pravasis IT Solution');
    $siteLogo = \App\Models\Setting::get('logo');
    
    $pages = \App\Models\Page::where('is_published', true)
        ->whereNull('parent_id')
        ->with('children')
        ->get();

    $headerMenu = \App\Models\Menu::getHeader();
    $footerMenu = \App\Models\Menu::getFooter();
    
    $navColor = \App\Models\Setting::get('nav_link_color', '#4b5563');
    $navHoverColor = \App\Models\Setting::get('nav_link_hover_color', '#2563eb');
    $navActiveColor = \App\Models\Setting::get('nav_link_active_color', '#2563eb');
    $adminBtnColor = \App\Models\Setting::get('admin_btn_color', '#2563eb');
    $adminBtnHoverColor = \App\Models\Setting::get('admin_btn_hover_color', '#1d4ed8');
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
            --admin-btn-bg: {{ $adminBtnColor }};
            --admin-btn-hover: {{ $adminBtnHoverColor }};
        }

        .nav-link { color: var(--nav-color); transition: color 0.3s ease; }
        .nav-link:hover { color: var(--nav-hover); }
        .nav-link.active { color: var(--nav-active) !important; border-bottom: 2px solid var(--nav-active) !important; }

        .admin-btn {
            background-color: var(--admin-btn-bg);
            color: white;
            transition: all 0.3s ease;
        }
        .admin-btn:hover {
            background-color: var(--admin-btn-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
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
                @php
                    $navItems = $headerMenu->count() > 0 ? $headerMenu : $pages->map(function($p) {
                        return (object)[
                            'label' => $p->title,
                            'url' => route('page.show', $p->slug),
                            'children' => $p->children->map(function($c) {
                                return (object)['label' => $c->title, 'url' => route('page.show', $c->slug)];
                            })
                        ];
                    });
                @endphp

                @foreach($navItems as $item)
                    @php 
                        $hasChildren = isset($item->children) && $item->children->count() > 0;
                        $itemUrl = url($item->url);
                        $isActive = request()->url() == $itemUrl || request()->is(ltrim(parse_url($itemUrl, PHP_URL_PATH), '/') . '/*');
                    @endphp
                    
                    @if($hasChildren)
                        <div class="relative group h-full flex items-center">
                            <button class="nav-link font-medium transition duration-150 py-2 flex items-center gap-1 {{ $isActive ? 'active' : '' }}">
                                {{ $item->label }}
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 top-[100%] w-48 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 py-2">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->url }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition">
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url }}" 
                           class="nav-link font-medium transition duration-150 py-2 {{ $isActive ? 'active' : '' }}">
                            {{ $item->label }}
                        </a>
                    @endif
                @endforeach
            </div>
            <div class="flex items-center space-x-4">
                 <a href="/admin" class="admin-btn px-4 py-2 text-sm font-semibold rounded-lg shadow-lg">
                    Admin Panel
                </a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 border-t border-gray-800 py-16 mt-20 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <!-- About Side -->
                <div class="col-span-1 md:col-span-1">
                    <a href="/" class="flex items-center space-x-3 mb-6">
                        @if($siteLogo)
                            <img src="{{ Storage::url($siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto">
                        @endif
                        <span class="text-xl font-bold text-white">
                            {{ $siteName }}
                        </span>
                    </a>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Leading provider of customized IT solutions and professional consultancy services.
                    </p>
                </div>

                <!-- Footer Menu -->
                <div class="col-span-1">
                    <h4 class="text-white font-bold mb-6">Quick Links</h4>
                    <ul class="space-y-4">
                        @if($footerMenu->count() > 0)
                            @foreach($footerMenu as $item)
                                <li><a href="{{ $item->url }}" class="text-gray-400 hover:text-blue-400 text-sm transition">{{ $item->label }}</a></li>
                            @endforeach
                        @else
                            @foreach($pages->take(5) as $p)
                                <li><a href="{{ route('page.show', $p->slug) }}" class="text-gray-400 hover:text-blue-400 text-sm transition">{{ $p->title }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-span-1">
                    <h4 class="text-white font-bold mb-6">Contact Us</h4>
                    <ul class="space-y-4 text-sm text-gray-400">
                        @if($email = \App\Models\Setting::get('email'))
                            <li class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $email }}
                            </li>
                        @endif
                        @if($phone = \App\Models\Setting::get('phone'))
                            <li class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $phone }}
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Social Links -->
                <div class="col-span-1">
                    <h4 class="text-white font-bold mb-6">Follow Us</h4>
                    <div class="flex space-x-4">
                        @php $socials = ['facebook', 'twitter', 'instagram', 'linkedin']; @endphp
                        @foreach($socials as $social)
                            @if($link = \App\Models\Setting::get($social))
                                <a href="{{ $link }}" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition group" target="_blank">
                                    <span class="sr-only">{{ ucfirst($social) }}</span>
                                    <!-- Simple text for now, could use icons -->
                                    <span class="text-xs group-hover:text-white">{{ strtoupper(substr($social, 0, 1)) }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-800 text-center text-gray-500">
                <p class="text-sm">
                    &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved. Professional CMS Development.
                </p>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
