<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    /**
     * Display the user's API tokens.
     */
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens()->latest()->get();
        return view('profile.api-tokens', compact('tokens'));
    }

    /**
     * Create a new API token for the user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = $request->user()->createToken($request->name);

        return back()->with([
            'success' => 'API token created successfully!',
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * Delete the specified API token.
     */
    public function destroy(Request $request, $tokenId)
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return back()->with('success', 'API token deleted successfully!');
    }
}
