<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware is now applied in the routes file
    }

    /**
     * Display a listing of the user's notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get the user's notifications
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get unread count
        $unreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(\App\Models\Notification $notification)
    {
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Mark the notification as read
        $notification->is_read = true;
        $notification->save();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        $user = auth()->user();

        // Mark all notifications as read
        \App\Models\Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
