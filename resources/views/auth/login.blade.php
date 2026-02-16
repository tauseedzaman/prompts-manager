<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white tracking-tight">Welcome back</h2>
        <p class="text-gray-400 mt-2">Sign in to manage your prompts collection.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-gray-300">
                Email Address
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fas fa-envelope"></i>
                </div>
                <input id="email" 
                       class="block w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all sm:text-sm" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       placeholder="you@example.com"
                       required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-gray-300">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-blue-400 hover:text-blue-300 transition-colors" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fas fa-lock"></i>
                </div>
                <input id="password" 
                       class="block w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all sm:text-sm"
                       type="password"
                       name="password"
                       placeholder="••••••••"
                       required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-white/10 bg-white/5 text-blue-600 focus:ring-blue-500/50 focus:ring-offset-0" name="remember">
            <label for="remember_me" class="ml-2 text-sm text-gray-400 select-none">Stay signed in</label>
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/50 transition-all duration-200">
            Sign In
        </button>

        <p class="text-center text-sm text-gray-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">Start for free</a>
        </p>
    </form>
</x-guest-layout>
