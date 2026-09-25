<?php

namespace App\View\Components\header;

use App\Models\Notifikasi;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationDropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $notifications = Notifikasi::with('obat')
            ->latest('id')
            ->take(10)
            ->get();

        $unreadCount = Notifikasi::whereDate('created_at', today())->count();

        return view('components.header.notification-dropdown', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }
}
