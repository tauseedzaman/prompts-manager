<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
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
                    ->orWhere('prompt_text', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->input('sort') === 'most_used') {
            $query->mostUsed();
        } else {
            $query->latest();
        }

        $prompts = $query->paginate($request->input('per_page', 12));

        return response()->json($prompts);
    }
}
