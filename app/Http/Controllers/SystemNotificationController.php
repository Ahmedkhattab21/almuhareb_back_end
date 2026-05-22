<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class SystemNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->currentUser();
        $guard = $this->currentGuard();

        $notifications = Notifications::query()
            ->forRecipient($user)
            ->latest()
            ->paginate(15);

        $unreadCount = Notifications::query()
            ->forRecipient($user)
            ->unread()
            ->count();

        $layout = match ($guard) {
            'company' => 'layouts.company',
            'lawyer' => 'layouts.lawyer',
            default => 'layouts.app',
        };

        return view('shared.notifications.index', compact(
            'notifications',
            'unreadCount',
            'guard',
            'layout'
        ));
    }

    public function open(string $id): RedirectResponse
    {
        $user = $this->currentUser();

        $notification = Notifications::query()
            ->forRecipient($user)
            ->findOrFail($id);

        $notification->markAsRead();

        if ($notification->url) {
            return redirect($notification->url);
        }

        return back();
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $user = $this->currentUser();

        $notification = Notifications::query()
            ->forRecipient($user)
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with('toast_success', __('notifications.messages.marked_read'));
    }

    public function markAllAsRead(): RedirectResponse
    {
        $user = $this->currentUser();

        Notifications::query()
            ->forRecipient($user)
            ->unread()
            ->update([
                'read_at' => now(),
            ]);

        return back()->with('toast_success', __('notifications.messages.all_marked_read'));
    }

    private function currentUser(): Model
    {
        foreach (['admin', 'company', 'lawyer', 'worker'] as $guard) {
            if (auth($guard)->check()) {
                return auth($guard)->user();
            }
        }

        abort(401);
    }

    private function currentGuard(): string
    {
        foreach (['admin', 'company', 'lawyer', 'worker'] as $guard) {
            if (auth($guard)->check()) {
                return $guard;
            }
        }

        abort(401);
    }
}
