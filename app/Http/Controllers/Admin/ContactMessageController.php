<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    /**
     * Display a paginated listing of incoming contact messages.
     */
    public function index(Request $request): View
    {
        $query = ContactMessage::latest();

        // Search by sender name, email, subject, or message content
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter by read/unread status
        $statusFilter = $request->input('status');
        if ($statusFilter === 'unread') {
            $query->unread();
        } elseif ($statusFilter === 'read') {
            $query->read();
        }

        $messages = $query->paginate(15)->withQueryString();

        // Summary counts for header stat cards
        $totalCount = ContactMessage::count();
        $unreadCount = ContactMessage::unread()->count();
        $readCount = ContactMessage::read()->count();

        return view('admin.messages.index', [
            'messages' => $messages,
            'totalCount' => $totalCount,
            'unreadCount' => $unreadCount,
            'readCount' => $readCount,
            'currentSearch' => $request->input('search', ''),
            'currentStatus' => $statusFilter,
        ]);
    }

    /**
     * Display the specified contact message and mark as read.
     */
    public function show(ContactMessage $message): View
    {
        $message->markAsRead();

        return view('admin.messages.show', [
            'message' => $message,
        ]);
    }

    /**
     * Toggle read/unread status of a message.
     */
    public function toggleRead(ContactMessage $message): RedirectResponse
    {
        if ($message->is_read) {
            $message->markAsUnread();
            $statusText = 'marked as unread';
        } else {
            $message->markAsRead();
            $statusText = 'marked as read';
        }

        return back()->with('status', "Message from {$message->name} {$statusText}.");
    }

    /**
     * Remove the specified contact message.
     */
    public function destroy(ContactMessage $message): RedirectResponse
    {
        $senderName = $message->name;
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('status', "Contact message from '{$senderName}' deleted successfully.");
    }
}
