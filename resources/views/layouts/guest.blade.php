<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ error: null, darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
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
        </style>
    </head>
    <body class="font-sans antialiased text-gray-200">
        <div class="fixed inset-0 z-[-1] bg-[#030712]">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(37,99,235,0.05),transparent_50%)]"></div>
            <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        <div class="absolute top-6 right-6">
            <button @click="darkMode = !darkMode" class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200 focus:outline-none">
                <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg x-show="darkMode" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 24a9 9 0 01-1.638-.15c-5.402-1.076-9.284-5.962-8.59-11.455.43-3.411 2.336-6.284 5.097-7.85-2.002-.56-4.14-.15-5.83.98A9.006 9.006 0 008.02 11.59 9.004 9.004 0 0016.96 23.63c.48.01.96-.02 1.434-.09.308-.046.616-.106.918-.182a.75.75 0 00.042-1.358z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                </svg>
            </button>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8 flex flex-col items-center">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <i class="fas fa-layer-group text-white text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold tracking-tight text-white">Promptly</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-[#09090b]/80 backdrop-blur-xl border border-white/5 shadow-2xl overflow-hidden sm:rounded-[2rem]">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Promptly. All rights reserved.
            </div>
        </div>
    </body>
</html>
