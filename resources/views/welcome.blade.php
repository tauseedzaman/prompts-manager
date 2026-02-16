<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ error: null, darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Prompts Manager') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
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
        </style>
    </head>
    <body class="antialiased font-sans bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 selection:bg-indigo-500 selection:text-white">
        <!-- Navigation -->
        <nav class="w-full flex items-center justify-between p-6 container mx-auto">
            <div class="flex items-center gap-2 font-bold text-xl">
                 <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                <span>Prompts Manager</span>
            </div>
            <div class="flex items-center gap-4">
                 <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-md text-gray-500 hover:bg-gray-200 focus:outline-none dark:text-gray-400 dark:hover:bg-gray-800 transition-all">
                    <i x-show="!darkMode" class="fas fa-sun"></i>
                    <i x-show="darkMode" x-cloak class="fas fa-moon"></i>
                </button>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/prompts') }}" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="container mx-auto px-6 py-16 text-center lg:py-32">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-6">
                Master Your <span class="text-indigo-600 dark:text-indigo-400">AI Prompts</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-10 max-w-2xl mx-auto">
                Stop losing your best prompts. Organize, categorize, and retrieve your engineering prompts instantly. Built for efficiency.
            </p>
            <div class="flex justify-center gap-4">
                 @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/prompts') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30">Go to Dashboard</a>
                    @else
                         @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30">Get Started for Free</a>
                        @else
                             <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30">Log In</a>
                        @endif
                    @endauth
                @endif
                <a href="#features" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Learn More</a>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="bg-white dark:bg-gray-800 py-20">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-12">
                    <!-- Feature 1 -->
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                             <i class="fas fa-layer-group text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Collections</h3>
                        <p class="text-gray-600 dark:text-gray-400">Group related prompts into collections for different projects, workflows, or models.</p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <i class="fas fa-tags text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Smart Tagging</h3>
                        <p class="text-gray-600 dark:text-gray-400">Add tags to your prompts to make them searchable and easy to filter in seconds.</p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400">
                             <i class="fas fa-copy text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Instant Copy</h3>
                        <p class="text-gray-600 dark:text-gray-400">Copy prompts to your clipboard with a single click and paste them directly into your AI tool.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-50 dark:bg-gray-900 py-12 border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-500 dark:text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Prompts Manager. All rights reserved.
                </div>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <span class="sr-only">GitHub</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </footer>
    </body>
</html>
