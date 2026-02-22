<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::orderBy('sort_order');

        if ($request->has('workspace_id') && !empty($request->workspace_id)) {
            $workspaceId = $request->workspace_id;
            if (!auth()->user()->allWorkspaces()->contains('id', $workspaceId)) {
                abort(403);
            }
            $query->where('workspace_id', $workspaceId);
        } else {
            $query->where('user_id', auth()->id())->whereNull('workspace_id');
        }

        $categories = $query->get();

        $allSuggestions = [
            ['name' => 'General', 'color' => '#64748b', 'icon' => 'fas fa-info-circle'],
            ['name' => 'Marketing', 'color' => '#3b82f6', 'icon' => 'fas fa-chart-line'],
            ['name' => 'Development', 'color' => '#10b981', 'icon' => 'fas fa-code'],
            ['name' => 'Social Media', 'color' => '#f59e0b', 'icon' => 'fas fa-share-alt'],
            ['name' => 'SEO', 'color' => '#8b5cf6', 'icon' => 'fas fa-search'],
            ['name' => 'Creative Writing', 'color' => '#ec4899', 'icon' => 'fas fa-pen-nib'],
            ['name' => 'Education', 'color' => '#06b6d4', 'icon' => 'fas fa-graduation-cap'],
            ['name' => 'Business', 'color' => '#475569', 'icon' => 'fas fa-briefcase'],
        ];

        $existingNames = $categories->pluck('name')->map(fn($n) => strtolower($n))->toArray();
        $suggestions = array_filter($allSuggestions, fn($s) => !in_array(strtolower($s['name']), $existingNames));

        return view('categories.index', compact('categories', 'suggestions'));
    }

    /**
     * Store a suggested resource in storage.
     */
    public function storeSuggestion(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['sort_order'] = 0;
        
        // Ensure not duplicate
        if (Category::where('user_id', auth()->id())->where('name', $validated['name'])->exists()) {
            return redirect()->back()->with('error', 'Category already exists.');
        }

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category added from suggestions.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255',
            ],
            'workspace_id' => 'nullable|exists:workspaces,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);
        
        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        if ($category->workspace_id) {
            if (!auth()->user()->allWorkspaces()->contains('id', $category->workspace_id)) {
                abort(403);
            }
        } elseif ($category->user_id !== auth()->id()) {
            abort(403);
        }
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        if ($category->workspace_id) {
            if (!auth()->user()->allWorkspaces()->contains('id', $category->workspace_id)) {
                abort(403);
            }
        } elseif ($category->user_id !== auth()->id()) {
            abort(403);
        }
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255',
                \Illuminate\Validation\Rule::unique('categories')
                    ->where('user_id', auth()->id())
                    ->ignore($category->id)
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
