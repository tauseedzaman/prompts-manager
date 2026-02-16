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
        <!-- How it Works Section -->
        <section id="how-it-works" class="py-20 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">How it Works</h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Get organized in minutes with our simple three-step workflow.</p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="relative p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold shadow-lg">1</div>
                        <h3 class="text-xl font-bold mb-3">Create Prompts</h3>
                        <p class="text-gray-600 dark:text-gray-400">Save your AI prompts with titles, categories, and tags for easy identification.</p>
                    </div>
                    <div class="relative p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold shadow-lg">2</div>
                        <h3 class="text-xl font-bold mb-3">Organize into Collections</h3>
                        <p class="text-gray-600 dark:text-gray-400">Group prompts into logical collections like 'Content Writing', 'Coding', or 'Marketing'.</p>
                    </div>
                    <div class="relative p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold shadow-lg">3</div>
                        <h3 class="text-xl font-bold mb-3">One-Click Copy</h3>
                        <p class="text-gray-600 dark:text-gray-400">Instantly copy any prompt and use it in ChatGPT, Claude, or any LLM of your choice.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-20 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Frequently Asked Questions</h2>
                </div>
                <div class="max-w-3xl mx-auto space-y-6">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                        <h4 class="text-lg font-bold mb-2">Is it free to use?</h4>
                        <p class="text-gray-600 dark:text-gray-400">Yes, the core features of Prompts Manager are completely free for individual users.</p>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                        <h4 class="text-lg font-bold mb-2">Can I share my prompts?</h4>
                        <p class="text-gray-600 dark:text-gray-400">Currently, prompts are private to your account. Sharing features are coming in a future update.</p>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                        <h4 class="text-lg font-bold mb-2">Is my data secure?</h4>
                        <p class="text-gray-600 dark:text-gray-400">Absolutely. We use industry-standard encryption and follow best practices to keep your data safe.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="py-20 bg-indigo-600">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-8">Ready to supercharge your AI game?</h2>
                <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto">Join thousands of engineers and creators who use Promptly to manage their AI prompt library.</p>
                <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 px-10 py-4 rounded-xl text-lg font-bold hover:bg-gray-100 transition-colors shadow-xl">Get Started Now</a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-50 dark:bg-gray-900 py-16 border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-12 mb-12">
                    <div class="col-span-2">
                        <div class="flex items-center gap-2 font-bold text-xl mb-4">
                            <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                            <span>Prompts Manager</span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                            The professional way to organize, store, and manage your AI prompts. Built for efficiency and productivity.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Product</h4>
                        <ul class="space-y-2 text-gray-500 dark:text-gray-400">
                            <li><a href="#features" class="hover:text-indigo-600 transition-colors">Features</a></li>
                            <li><a href="#how-it-works" class="hover:text-indigo-600 transition-colors">How it Works</a></li>
                            <li><a href="#faq" class="hover:text-indigo-600 transition-colors">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Links</h4>
                        <ul class="space-y-2 text-gray-500 dark:text-gray-400">
                            <li><a href="{{ route('login') }}" class="hover:text-indigo-600 transition-colors">Log In</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-indigo-600 transition-colors">Register</a></li>
                        </ul>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-gray-200 dark:border-gray-800">
                    <div class="text-gray-500 dark:text-gray-400 text-sm mb-4 md:mb-0">
                        &copy; {{ date('Y') }} Prompts Manager. All rights reserved.
                    </div>
                    <div class="flex gap-6">
                        <a href="#" class="text-gray-400 hover:text-indigo-600 transition-colors">
                            <i class="fab fa-github text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-600 transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-600 transition-colors">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
