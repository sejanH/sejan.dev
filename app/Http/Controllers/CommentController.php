<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    /**
     * Handle public comment submission with manual approval workflow.
     */
    public function store(Request $request, Post $post): RedirectResponse
    {
        $throttleKey = 'comment|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->with('error', 'Too many comments submitted. Please wait a few minutes before trying again.');
        }

        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:100'],
            'author_email' => ['required', 'email', 'max:150'],
            'author_url' => ['nullable', 'url', 'max:255'],
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        RateLimiter::hit($throttleKey, 300);

        Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->check() ? auth()->id() : null,
            'parent_id' => $validated['parent_id'] ?? null,
            'author_name' => trim(strip_tags($validated['author_name'])),
            'author_email' => trim(strtolower($validated['author_email'])),
            'author_url' => !empty($validated['author_url']) ? trim($validated['author_url']) : null,
            'content' => trim(strip_tags($validated['content'])),
            'status' => 'pending', // Strictly pending until manually approved
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()
            ->with('status', 'Thank you! Your comment has been submitted and is awaiting manual administrator approval.')
            ->with('success', 'Thank you! Your comment has been submitted and is awaiting manual administrator approval.');
    }
}
