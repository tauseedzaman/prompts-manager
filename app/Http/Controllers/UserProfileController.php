<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    /**
     * Display the specified user's public profile.
     */
    public function show($identifier)
    {
        $user = User::where('username', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();
        
        $prompts = $user->prompts()
            ->public()
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(12);

        $stats = [
            'prompts_count' => $user->prompts()->public()->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
        ];

        return view('users.show', compact('user', 'prompts', 'stats'));
    }

    /**
     * Follow or unfollow the specified user.
     */
    public function follow(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        $follower = auth()->user();
        
        if ($follower->isFollowing($user)) {
            $follower->following()->detach($user->id);
            $message = "You unfollowed {$user->name}.";
        } else {
            $follower->following()->attach($user->id);
            $message = "You are now following {$user->name}!";
        }

        return back()->with('success', $message);
    }
}
