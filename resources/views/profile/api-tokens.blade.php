<x-app-layout>
    @section('title', 'API Tokens')

    <div class="content-wrapper">
        <div class="page-header">
            <h1 class="page-title">API Tokens</h1>
            <p class="page-description">Manage your API tokens for browser extension integration</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- New Token Created Alert -->
        @if(session('token'))
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200 px-4 py-3 rounded-lg mb-6">
                <p class="font-semibold mb-2">Your new API token:</p>
                <div class="bg-white dark:bg-gray-800 rounded p-3 font-mono text-sm break-all border border-blue-300 dark:border-blue-700">
                    {{ session('token') }}
                </div>
                <p class="text-sm mt-2 text-blue-600 dark:text-blue-400">⚠️ Make sure to copy this token now. You won't be able to see it again!</p>
            </div>
        @endif

        <!-- Create New Token Form -->
        <div class="card mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Create New Token</h2>
            <form action="{{ route('api-tokens.store') }}" method="POST" class="flex gap-3">
                @csrf
                <input 
                    type="text" 
                    name="name" 
                    placeholder="Token name (e.g., Browser Extension)" 
                    required
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                >
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Create Token
                </button>
            </form>
        </div>

        <!-- Existing Tokens List -->
        <div class="card">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Your API Tokens</h2>
            
            @if($tokens->count() > 0)
                <div class="space-y-3">
                    @foreach($tokens as $token)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $token->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Created {{ $token->created_at->diffForHumans() }}</p>
                                @if($token->last_used_at)
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Last used {{ $token->last_used_at->diffForHumans() }}</p>
                                @else
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Never used</p>
                                @endif
                            </div>
                            <form action="{{ route('api-tokens.destroy', $token->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this token?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium transition">
                                    Revoke
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">You haven't created any API tokens yet.</p>
            @endif
        </div>

        <!-- Back to Profile Link -->
        <div class="mt-6">
            <a href="{{ route('profile.edit') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                ← Back to Profile
            </a>
        </div>
    </div>
</x-app-layout>
