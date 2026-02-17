<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Prompt;
use App\Models\PromptRating;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Prompt::public()->with(['category', 'tags', 'user', 'ratings']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('prompt_text', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        $prompts = $query->latest()->paginate(12);
        $categories = Category::whereHas('prompts', function ($q) {
            $q->public();
        })->get();

        return view('marketplace.index', compact('prompts', 'categories'));
    }

    public function show(Prompt $prompt)
    {
        if ($prompt->visibility !== 'public' && $prompt->user_id !== auth()->id()) {
            abort(403);
        }

        $prompt->load(['category', 'tags', 'user', 'ratings.user']);

        $userRating = auth()->check() 
            ? $prompt->ratings()->where('user_id', auth()->id())->first() 
            : null;

        return view('marketplace.show', compact('prompt', 'userRating'));
    }

    public function welcome()
    {
        $featuredPrompts = collect();

        // 1. Top rated prompt
        $topRated = Prompt::public()
            ->with(['category', 'user', 'ratings'])
            ->withCount('ratings')
            ->get()
            ->sortByDesc('average_rating')
            ->first();
        if ($topRated) {
            $featuredPrompts->push($topRated);
        }

        // 2. Most forked prompt
        $mostForked = Prompt::public()
            ->with(['category', 'user'])
            ->withCount('forks')
            ->orderBy('forks_count', 'desc')
            ->whereNotIn('id', $featuredPrompts->pluck('id'))
            ->first();
        if ($mostForked) {
            $featuredPrompts->push($mostForked);
        }

        // 3. Random public prompt
        $random = Prompt::public()
            ->with(['category', 'user'])
            ->whereNotIn('id', $featuredPrompts->pluck('id'))
            ->inRandomOrder()
            ->first();
        if ($random) {
            $featuredPrompts->push($random);
        }
        $stats = [
            'prompts' => \App\Models\Prompt::count(),
            'forks' => \App\Models\Prompt::where('forked_from_id', '!=', null)->count(), // adjust model/table
            'creators' => \App\Models\User::has('prompts')->count(),
            'exports' => \App\Models\User::count(),
        ];

        return view('welcome', compact('featuredPrompts', 'stats'));

    }

    public function rate(Request $request, Prompt $prompt)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        PromptRating::updateOrCreate(
            ['user_id' => auth()->id(), 'prompt_id' => $prompt->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return back()->with('success', 'Thank you for your rating!');
    }
}
