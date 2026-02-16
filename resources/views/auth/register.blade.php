<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white tracking-tight">Create your account</h2>
        <p class="text-gray-400 mt-2">Start organizing your AI prompts like a pro.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-gray-300">Full Name</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fas fa-user"></i>
                </div>
                <input id="name" 
                       class="block w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all sm:text-sm"
                       type="text" 
                       name="name" 
                       :value="old('name')" 
                       placeholder="John Doe"
                       required autofocus autocomplete="name" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
        </div>

        <!-- Email Address -->
        <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
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
                       required autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fas fa-lock"></i>
                </div>
                <input id="password" 
                       class="block w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all sm:text-sm"
                       type="password"
                       name="password"
                       placeholder="••••••••"
                       required autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-1">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fas fa-check-circle"></i>
                </div>
                <input id="password_confirmation" 
                       class="block w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all sm:text-sm"
                       type="password"
                       name="password_confirmation" 
                       placeholder="••••••••"
                       required autocomplete="new-password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/50 transition-all duration-200">
            Create Account
        </button>

        <p class="text-center text-sm text-gray-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">Sign in here</a>
        </p>
    </form>
</x-guest-layout>
