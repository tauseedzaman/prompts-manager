<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        error: null,
        darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        faqOpen: 1
    }"
    :class="{ 'dark': darkMode }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Prompts Manager') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display:none !important; }
        .glass {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .noise {
            background-image: radial-gradient(rgba(255,255,255,.08) 1px, transparent 1px);
            background-size: 18px 18px;
        }
    </style>
</head>

<body class="antialiased font-sans bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 selection:bg-indigo-500 selection:text-white">

<!-- Top Nav (sticky + glass) -->
<nav class="sticky top-0 z-50 border-b border-gray-200/60 dark:border-gray-800/60 bg-white/70 dark:bg-gray-950/60 glass">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3 font-extrabold tracking-tight">
            <span class="w-10 h-10 rounded-xl bg-indigo-600/10 dark:bg-indigo-500/10 flex items-center justify-center">
                <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400 text-xl"></i>
            </span>
            <span class="text-lg md:text-xl">Prompts Manager</span>
            <span class="hidden md:inline-flex items-center px-2 py-1 text-[11px] font-bold rounded-full bg-gray-900 text-white dark:bg-white dark:text-gray-900">
                OSS
            </span>
        </a>

        <div class="flex items-center gap-3 md:gap-6">
            <a href="#features" class="hidden md:inline font-semibold text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                Features
            </a>
            <a href="{{ route('marketplace.index') }}" class="font-semibold text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                Marketplace
            </a>

            <!-- Dark Mode Toggle -->
            <button
                @click="darkMode = !darkMode"
                class="p-2 rounded-lg text-gray-600 hover:bg-gray-200/70 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-800/70 transition-all"
                aria-label="Toggle dark mode"
            >
                <i x-show="!darkMode" class="fas fa-sun"></i>
                <i x-show="darkMode" x-cloak class="fas fa-moon"></i>
            </button>

            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/prompts') }}"
                       class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm shadow-indigo-500/20">
                        Dashboard <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm shadow-indigo-500/20">
                            Get Started <i class="fas fa-bolt text-xs"></i>
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

<!-- Hero -->
<header class="relative overflow-hidden">
    <!-- background blobs -->
    <div class="absolute inset-0 -z-10">
        <div class="absolute -top-40 -left-40 w-[520px] h-[520px] rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-[520px] h-[520px] rounded-full bg-purple-500/20 blur-3xl"></div>
        <div class="absolute inset-0 noise opacity-40 dark:opacity-25"></div>
    </div>

    <div class="container mx-auto px-6 py-16 lg:py-24">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex flex-wrap justify-center gap-2 mb-6">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-600/10 text-indigo-700 dark:text-indigo-300 dark:bg-indigo-500/10">
                    Save + Reuse Prompts
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-600/10 text-purple-700 dark:text-purple-300 dark:bg-purple-500/10">
                    Marketplace + Forking
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-600/10 text-emerald-700 dark:text-emerald-300 dark:bg-emerald-500/10">
                    Import / Export JSON
                </span>
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-5">
                Master Your <span class="text-indigo-600 dark:text-indigo-400">AI Prompts</span> like a product.
            </h1>

            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-10">
                Stop losing your best prompts in random notes. Organize, version, share, and fork prompts with a clean workflow built for builders.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/prompts') }}"
                           class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-lg font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                            Go to Dashboard
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-lg font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                                Get Started for Free
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-lg font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                                Log In
                            </a>
                        @endif
                    @endauth
                @endif

                <a href="{{ route('marketplace.index') }}"
                   class="bg-white/80 dark:bg-gray-900/60 text-gray-800 dark:text-gray-200 border border-gray-200/70 dark:border-gray-700/70 px-8 py-3 rounded-xl text-lg font-semibold hover:bg-white dark:hover:bg-gray-900 transition shadow-sm">
                    Browse Marketplace
                </a>
            </div>

            <!-- mini social proof -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-gray-900 text-white dark:bg-white dark:text-gray-900 flex items-center justify-center font-bold">P</span>
                    <span><span class="font-semibold text-gray-800 dark:text-gray-200">Prompt-first</span> workflow</span>
                </div>
                <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-gray-800"></div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-shield-halved text-indigo-600 dark:text-indigo-400"></i>
                    <span>Own your library</span>
                </div>
                <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-gray-800"></div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-code-branch text-purple-600 dark:text-purple-400"></i>
                    <span>Fork & remix community prompts</span>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Stats strip -->
<section class="border-y border-gray-200/70 dark:border-gray-800/70 bg-white/60 dark:bg-gray-950/60">
    <div class="container mx-auto px-6 py-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800/60">
                <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
                    {{ number_format($stats['prompts'] ?? 1200) }}+
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Prompts saved</div>
            </div>
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800/60">
                <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
                    {{ number_format($stats['forks'] ?? 420) }}+
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Forks created</div>
            </div>
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800/60">
                <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
                    {{ number_format($stats['creators'] ?? 180) }}+
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Creators</div>
            </div>
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800/60">
                <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
                    {{ number_format($stats['exports'] ?? 650) }}+
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Exports</div>
            </div>
        </div>

        <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-500">
            Tip: pass real numbers via <code class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800/60">$stats</code>
            from controller (optional).
        </p>
    </div>
</section>

<!-- Features -->
<section id="features" class="py-20">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">Built for speed, clarity, and reuse</h2>
            <p class="mt-3 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Everything you need to store your best prompts and actually find them again.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="group p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/70 dark:border-gray-800/70 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 dark:bg-indigo-500/10 flex items-center justify-center mb-5">
                    <i class="fas fa-file-export text-xl"></i>
                </div>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Import & Export</h3>
                    <span class="text-[11px] font-bold px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">Portable</span>
                </div>
                <p class="mt-3 text-gray-600 dark:text-gray-400">
                    Move your library anywhere with clean JSON import/export and share prompts instantly.
                </p>
            </div>

            <div class="group p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/70 dark:border-gray-800/70 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-purple-600/10 text-purple-600 dark:text-purple-400 dark:bg-purple-500/10 flex items-center justify-center mb-5">
                    <i class="fas fa-tags text-xl"></i>
                </div>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Smart Tagging</h3>
                    <span class="text-[11px] font-bold px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">Fast filters</span>
                </div>
                <p class="mt-3 text-gray-600 dark:text-gray-400">
                    Tags + categories make discovery instant. Search by topic, tone, model, or use-case.
                </p>
            </div>

            <div class="group p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/70 dark:border-gray-800/70 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 dark:bg-emerald-500/10 flex items-center justify-center mb-5">
                    <i class="fas fa-copy text-xl"></i>
                </div>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Instant Copy</h3>
                    <span class="text-[11px] font-bold px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">1-click</span>
                </div>
                <p class="mt-3 text-gray-600 dark:text-gray-400">
                    Copy a prompt in one click and paste directly into ChatGPT / Claude / Gemini / Ollama.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Prompts -->
@if($featuredPrompts->count() > 0)
<section id="featured-prompts" class="py-20 bg-white/70 dark:bg-gray-950/40 border-y border-gray-200/70 dark:border-gray-800/70">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-3">Featured Community Prompts</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Handpicked prompts that are high-rated and easy to remix.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-12">
            @foreach($featuredPrompts as $prompt)
                <div class="group bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/70 dark:border-gray-800/70 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
                    <div class="p-6 flex-1">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-indigo-600/10 text-indigo-700 dark:text-indigo-300 dark:bg-indigo-500/10">
                                <i class="fas fa-folder-open text-[11px]"></i> {{ $prompt->category->name }}
                            </span>

                            <div class="inline-flex items-center gap-1 text-amber-500 text-sm font-bold">
                                <i class="fas fa-star"></i>
                                <span>{{ $prompt->average_rating }}</span>
                            </div>
                        </div>

                        <h3 class="text-lg font-extrabold mb-2 line-clamp-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                            {{ $prompt->title }}
                        </h3>

                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-5 line-clamp-3">
                            {{ $prompt->description ?? 'No description provided.' }}
                        </p>

                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($prompt->user->name, 0, 1) }}
                                </div>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $prompt->user->name }}</span>
                            </div>

                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-code-branch mr-1"></i>
                                {{ $prompt->forks_count ?? $prompt->forks()->count() }}
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200/60 dark:border-gray-800/60 flex justify-between items-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Open → Fork → Remix</span>
                        <a href="{{ route('marketplace.show', $prompt) }}" class="text-indigo-600 dark:text-indigo-400 font-extrabold hover:underline">
                            View Prompt <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center">
            <a href="{{ route('marketplace.index') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 text-white px-8 py-3 rounded-xl text-lg font-semibold hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/25">
                Explore Full Marketplace <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- How it Works -->
<section id="how-it-works" class="py-20">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-3">How it Works</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Three steps. No fluff. You’ll feel organized in minutes.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @php
                $steps = [
                    ['n'=>1,'t'=>'Create Prompts','d'=>'Save prompts with title, category, tags, and descriptions so they stay reusable.','i'=>'fa-pen-to-square'],
                    ['n'=>2,'t'=>'Share Publicly','d'=>'Publish prompts to the marketplace so others can learn and you can build a profile.','i'=>'fa-globe'],
                    ['n'=>3,'t'=>'Fork & Remix','d'=>'Fork any prompt into your library and customize it for your workflow.','i'=>'fa-code-branch'],
                ];
            @endphp

            @foreach($steps as $s)
                <div class="p-8 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/70 dark:border-gray-800/70 shadow-sm hover:shadow-md transition relative">
                    <div class="absolute -top-4 -left-4 w-11 h-11 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-extrabold shadow-lg">
                        {{ $s['n'] }}
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gray-900/5 dark:bg-white/5 flex items-center justify-center text-gray-900 dark:text-gray-200 mb-5">
                        <i class="fas {{ $s['i'] }} text-xl"></i>
                    </div>
                    <h3 class="text-xl font-extrabold mb-3">{{ $s['t'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $s['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ (accordion) -->
<section id="faq" class="py-20 bg-white/70 dark:bg-gray-950/40 border-y border-gray-200/70 dark:border-gray-800/70">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white">Frequently Asked Questions</h2>
            <p class="mt-3 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Quick answers to the common stuff.
            </p>
        </div>

        <div class="max-w-3xl mx-auto space-y-4">
            @php
                $faqs = [
                    ['q'=>'Is it free to use?','a'=>'Yes — the core features are free for individual users.'],
                    ['q'=>'Can I share my prompts?','a'=>'Yes! Export to JSON, share with anyone, or import into another account in seconds.'],
                    ['q'=>'Is my data secure?','a'=>'Yes. We follow best practices to protect your data and keep accounts safe.'],
                    ['q'=>'Can I fork prompts from the marketplace?','a'=>'Absolutely — fork a prompt to your library and remix it however you like.'],
                ];
            @endphp

            @foreach($faqs as $idx => $f)
                <div class="rounded-2xl border border-gray-200/70 dark:border-gray-800/70 bg-white dark:bg-gray-900 overflow-hidden">
                    <button
                        class="w-full flex items-center justify-between gap-4 p-6 text-left"
                        @click="faqOpen = (faqOpen === {{ $idx+1 }} ? 0 : {{ $idx+1 }})"
                    >
                        <span class="font-extrabold text-gray-900 dark:text-white">{{ $f['q'] }}</span>
                        <span class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-700 dark:text-gray-200">
                            <i class="fas" :class="faqOpen === {{ $idx+1 }} ? 'fa-minus' : 'fa-plus'"></i>
                        </span>
                    </button>

                    <div x-show="faqOpen === {{ $idx+1 }}" x-collapse class="px-6 pb-6 text-gray-600 dark:text-gray-400">
                        {{ $f['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20">
    <div class="container mx-auto px-6">
        <div class="rounded-3xl bg-indigo-600 overflow-hidden relative">
            <div class="absolute inset-0 opacity-20 noise"></div>
            <div class="absolute -top-24 -right-24 w-[420px] h-[420px] rounded-full bg-white/20 blur-3xl"></div>

            <div class="relative p-10 md:p-14 text-center">
                <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6">
                    Ready to supercharge your AI workflow?
                </h2>
                <p class="text-lg md:text-xl text-indigo-100 mb-10 max-w-2xl mx-auto">
                    Build a personal prompt library you’ll actually reuse — and discover community prompts you can fork instantly.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center gap-2 bg-white text-indigo-700 px-10 py-4 rounded-2xl text-lg font-extrabold hover:bg-gray-100 transition shadow-xl">
                            Get Started Now <i class="fas fa-arrow-right text-sm"></i>
                        </a>
                    @endif
                    <a href="{{ route('marketplace.index') }}"
                       class="inline-flex items-center justify-center gap-2 bg-indigo-700/40 text-white border border-white/20 px-10 py-4 rounded-2xl text-lg font-extrabold hover:bg-indigo-700/60 transition">
                        Explore Marketplace <i class="fas fa-compass text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-14 border-t border-gray-200/70 dark:border-gray-800/70">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-10 mb-10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 font-extrabold text-xl mb-4">
                    <span class="w-10 h-10 rounded-xl bg-indigo-600/10 dark:bg-indigo-500/10 flex items-center justify-center">
                        <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </span>
                    <span>Prompts Manager</span>
                </div>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                    The professional way to organize, store, fork, and share AI prompts — built for builders.
                </p>

                <div class="mt-5 flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800/60">
                        <i class="fas fa-code"></i> Laravel + Tailwind
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-900 border border-gray-200/60 dark:border-gray-800/60">
                        <i class="fas fa-bolt"></i> Fast workflow
                    </span>
                </div>
            </div>

            <div>
                <h4 class="font-extrabold mb-4">Product</h4>
                <ul class="space-y-2 text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Marketplace</a></li>
                    <li><a href="#features" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Features</a></li>
                    <li><a href="#how-it-works" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">How it Works</a></li>
                    <li><a href="#faq" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-extrabold mb-4">Links</h4>
                <ul class="space-y-2 text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('login') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Log In</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Register</a></li>
                </ul>

                <div class="mt-5 flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" aria-label="GitHub">
                        <i class="fab fa-github text-2xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" aria-label="X/Twitter">
                        <i class="fab fa-twitter text-2xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" aria-label="LinkedIn">
                        <i class="fab fa-linkedin text-2xl"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-gray-200/70 dark:border-gray-800/70">
            <div class="text-gray-500 dark:text-gray-400 text-sm mb-4 md:mb-0">
                &copy; {{ date('Y') }} Prompts Manager. All rights reserved.
            </div>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
                Built to help you ship faster ⚡
            </div>
        </div>
    </div>
</footer>

</body>
</html>
