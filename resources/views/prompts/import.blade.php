@extends('layouts.app')

@section('title', 'Import Prompts')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('prompts.index') }}" class="btn btn-secondary btn-sm rounded-full w-10 h-10 flex items-center justify-center p-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold tracking-tight">Import Prompts</h1>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="md:col-span-1 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    Instructions
                </h3>
                <ul class="space-y-4 text-sm text-muted">
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-600/10 text-blue-500 flex items-center justify-center font-bold text-[10px]">1</span>
                        <span>Upload a <strong>JSON</strong> file containing an array of prompts.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-600/10 text-blue-500 flex items-center justify-center font-bold text-[10px]">2</span>
                        <span>Categories and tags will be <strong>automatically mapped</strong> or created if they don't exist.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-600/10 text-blue-500 flex items-center justify-center font-bold text-[10px]">3</span>
                        <span>Duplicate titles will be handled by adding a suffix to the <strong>slug</strong>.</span>
                    </li>
                </ul>
            </div>

            <div class="card p-6 bg-blue-600/5 border-blue-500/10">
                <h3 class="text-lg font-bold mb-2">Need a template?</h3>
                <p class="text-sm text-muted mb-4">Download our sample JSON file to see the required structure.</p>
                <a href="{{ route('prompts.sample') }}" class="btn btn-secondary w-full">
                    <i class="fas fa-download"></i> Sample JSON
                </a>
            </div>
        </div>

        <!-- Import Form -->
        <div class="md:col-span-2">
            <div class="card p-8">
                <form action="{{ route('prompts.import') }}" method="POST" enctype="multipart/form-data" x-data="{ dragging: false, fileName: '' }">
                    @csrf
                    <div class="mb-8">
                        <label class="block text-center mb-4">
                            <span class="text-xl font-bold">Upload your file</span>
                        </label>
                        
                        <div class="relative group" 
                             :class="{ 'border-blue-500 bg-blue-500/5': dragging }"
                             @dragover.prevent="dragging = true"
                             @dragleave.prevent="dragging = false"
                             @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name">
                            
                            <input type="file" name="file" accept=".json" required x-ref="fileInput"
                                   @change="fileName = $el.files[0].name"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="border-2 border-dashed border-gray-200 dark:border-white/10 rounded-3xl p-12 text-center transition-all group-hover:border-blue-500/50 group-hover:bg-blue-500/5">
                                <div class="w-16 h-16 bg-blue-600/10 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-export text-3xl"></i>
                                </div>
                                <h4 class="text-lg font-semibold mb-2" x-text="fileName || 'Click to upload or drag and drop'"></h4>
                                <p class="text-sm text-muted">Maximum file size: 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-white/5">
                        <button type="submit" class="btn btn-primary px-8">
                            <i class="fas fa-upload"></i>
                            Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
