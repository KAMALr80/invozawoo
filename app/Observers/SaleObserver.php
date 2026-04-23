<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        $performerName = auth()->user()->name ?? 'System';
        $title = "New Sale Created";
        $message = "Sale #{$sale->invoice_no} was created by {$performerName}. Total: " . number_format($sale->total_amount, 2);
        
        // Notify Admins and the creator
        $recipients = User::whereIn('role', ['admin', 'staff'])->get();
        $url = route('sales.show', $sale->id);
        $icon = '💰';
        Notification::send($recipients, new SystemNotification($title, $message, $url, $icon));
    }
}
