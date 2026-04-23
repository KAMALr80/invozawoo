<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the current user.
     */
    public function getUnread()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['count' => 0, 'notifications' => []]);

        // 1. Get real unread notifications from database
        $unreadNotifications = $user->unreadNotifications;
        $count = $unreadNotifications->count();
        
        $notifications = $unreadNotifications->take(10)->map(function($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'System Alert',
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'icon' => $n->data['icon'] ?? 'fas fa-bell',
                'time' => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                'is_read' => false
            ];
        })->toArray();

        // 2. Fallback: If unread list is short, fill with Recent System Activities
        if (count($notifications) < 5) {
            // Fetch latest from various models
            $recentActivities = [];
            
            // Recent Sales
            $sales = \App\Models\Sale::latest()->take(3)->get()->map(fn($s) => [
                'id' => 's_'.$s->id,
                'title' => 'Sale Created',
                'message' => "Sale #{$s->invoice_no} by " . ($s->user->name ?? 'System'),
                'url' => route('sales.index'),
                'icon' => 'fas fa-shopping-cart',
                'time_raw' => $s->created_at
            ]);

            // Recent Shipments
            $shipments = \App\Models\Shipment::latest()->take(3)->get()->map(fn($s) => [
                'id' => 'sh_'.$s->id,
                'title' => 'Shipment Update',
                'message' => "Shipment #{$s->shipment_number} status: " . ucfirst($s->status),
                'url' => route('logistics.shipments.index'),
                'icon' => 'fas fa-truck',
                'time_raw' => $s->created_at
            ]);

            $merged = $sales->concat($shipments)->sortByDesc('time_raw')->take(5);

            foreach($merged as $m) {
                // Check if already in notifications list to avoid duplicates
                $exists = false;
                foreach($notifications as $n) {
                    if ($n['message'] == $m['message']) { $exists = true; break; }
                }

                if (!$exists) {
                    $notifications[] = [
                        'id' => $m['id'],
                        'title' => $m['title'],
                        'message' => $m['message'],
                        'url' => $m['url'],
                        'icon' => $m['icon'],
                        'time' => $m['time_raw']->diffForHumans(),
                        'is_read' => true
                    ];
                }
            }
        }
        
        return response()->json([
            'count' => $count,
            'notifications' => array_values($notifications)
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
