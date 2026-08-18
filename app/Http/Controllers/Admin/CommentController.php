<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Display listing of comments with status filters and counts.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        $search = $request->query('q');

        $query = Comment::with(['post', 'user', 'parent'])
            ->latest('created_at');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('author_name', 'like', "%{$search}%")
                  ->orWhere('author_email', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $comments = $query->paginate(20)->withQueryString();

        $counts = [
            'pending' => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
            'trash' => Comment::where('status', 'trash')->count(),
        ];

        return view('admin.comments.index', [
            'comments' => $comments,
            'currentStatus' => $status,
            'counts' => $counts,
            'search' => $search,
        ]);
    }

    /**
     * Update comment status (approve, reject, spam, trash).
     */
    public function updateStatus(Request $request, Comment $comment): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,pending,spam,trash'],
        ]);

        $comment->update(['status' => $validated['status']]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Comment marked as {$validated['status']}.",
                'comment' => $comment,
            ]);
        }

        return back()->with('status', "Comment marked as {$validated['status']}.");
    }

    /**
     * Post an administrative reply directly to a comment.
     */
    public function reply(Request $request, Comment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $admin = auth()->user();

        Comment::create([
            'post_id' => $comment->post_id,
            'user_id' => $admin->id,
            'parent_id' => $comment->id,
            'author_name' => $admin->name,
            'author_email' => $admin->email,
            'content' => $validated['content'],
            'status' => 'approved', // Admin replies are auto-approved
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('status', 'Reply posted successfully.');
    }

    /**
     * Permanently delete a comment.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();
        return back()->with('status', 'Comment permanently deleted.');
    }
}
