<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ error: null, darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Fallback if CSS loading fails or for inline tweaks */
        /* Will populate form css if needed, but assuming app.css covers it */
    </style>
</head>
<body class="font-sans antialiased">
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <i class="fas fa-layer-group text-blue-500"></i>
                <span class="tracking-tight">Promptly</span>
            </div>
            
            <nav>
                <div class="nav-section">
                    <a href="{{ route('prompts.index') }}" class="nav-link {{ request()->routeIs('prompts.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        All Prompts
                    </a>
                    <a href="{{ route('prompts.favorites') }}" class="nav-link {{ request()->routeIs('prompts.favorites') ? 'active' : '' }}">
                        <i class="fas fa-heart"></i>
                        Favorites
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Library</div>
                    
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i>
                        Categories
                    </a>
                    <a href="{{ route('collections.index') }}" class="nav-link {{ request()->routeIs('collections.*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i>
                        Collections
                    </a>
                    <a href="{{ route('tags.index') }}" class="nav-link {{ request()->routeIs('tags.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        Tags
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="search-bar">
                    <form action="{{ route('prompts.index') }}" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Search prompts..." class="search-input pr-10" value="{{ request('search') }}">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="actions flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none dark:text-gray-400 dark:hover:bg-gray-700">
                        <!-- Sun Icon -->
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon Icon -->
                        <svg x-show="darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 24a9 9 0 01-1.638-.15c-5.402-1.076-9.284-5.962-8.59-11.455.43-3.411 2.336-6.284 5.097-7.85-2.002-.56-4.14-.15-5.83.98A9.006 9.006 0 008.02 11.59 9.004 9.004 0 0016.96 23.63c.48.01.96-.02 1.434-.09.308-.046.616-.106.918-.182a.75.75 0 00.042-1.358z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </button>
                    <a href="{{ route('prompts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        New Prompt
                    </a>

                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-[#0c0c0e] rounded-xl shadow-2xl py-1 z-50 border border-white/5 ring-1 ring-black ring-opacity-5" style="display: none;">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-400 hover:bg-white/5 hover:text-white">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-400 hover:bg-white/5 hover:text-white">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
                <!-- Support for $slot if used by Breeze components -->
                {{ $slot ?? '' }}
            </div>
        </main>
    </div>
    
    <script>
        // Simple clipboard copy script with SweetAlert
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Copied to clipboard!',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }).catch(err => {
                console.error('Failed to copy: ', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to copy to clipboard',
                    toast: true,
                    position: 'top-end'
                });
            });
        }
        
        // Show success/error messages from session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end'
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
