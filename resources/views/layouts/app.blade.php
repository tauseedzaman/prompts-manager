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
                    @auth
                        <a href="{{ route('prompts.index') }}" class="nav-link {{ request()->routeIs('prompts.index') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            All Prompts
                        </a>
                        <a href="{{ route('prompts.favorites') }}" class="nav-link {{ request()->routeIs('prompts.favorites') ? 'active' : '' }}">
                            <i class="fas fa-heart"></i>
                            Favorites
                        </a>
                    @endauth
                    <a href="{{ route('marketplace.index') }}" class="nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
                        <i class="fas fa-globe"></i>
                        Marketplace
                    </a>
                </div>

                @auth
                    <div class="nav-section">
                        <div class="nav-section-title">Workspaces</div>
                        
                        <div class="px-3 mb-2">
                             <select onchange="window.location.href='/prompts?workspace_id=' + this.value" class="w-full bg-[#1e293b] border-none text-xs text-muted rounded-lg focus:ring-1 focus:ring-indigo-500 py-2">
                                <option value="">Private Library</option>
                                @foreach(auth()->user()->allWorkspaces() as $ws)
                                    <option value="{{ $ws->id }}" {{ request('workspace_id') == $ws->id ? 'selected' : '' }}>
                                        {{ $ws->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <a href="{{ route('workspaces.index') }}" class="nav-link {{ request()->routeIs('workspaces.*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            Manage Teams
                        </a>
                    </div>

                    <div class="nav-section">
                        <div class="nav-section-title">Library</div>
                        
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder"></i>
                            Categories
                        </a>

                        <a href="{{ route('tags.index') }}" class="nav-link {{ request()->routeIs('tags.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i>
                            Tags
                        </a>
                    </div>
                @endauth
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

                    @auth
                        <a href="{{ route('prompts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            New Prompt
                        </a>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none group">
                                <img class="h-8 w-8 rounded-full object-cover border border-indigo-500/30 group-hover:border-indigo-500 transition-colors" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                                <i class="fas fa-chevron-down text-[10px] text-gray-500"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-[#0c0c0e] rounded-xl shadow-2xl py-2 z-50 border border-white/5 ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('users.show', auth()->user()->username ?? auth()->user()->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-400 hover:bg-white/5 hover:text-white">
                                    <i class="fas fa-user-circle mr-3"></i> My Profile
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 hover:bg-white/5 hover:text-white">
                                    <i class="fas fa-cog mr-3"></i> Profile Settings
                                </a>
                                <div class="border-t border-white/5 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300">
                                        <i class="fas fa-sign-out-alt mr-3"></i> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Log In</a>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
                        </div>
                    @endauth
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
