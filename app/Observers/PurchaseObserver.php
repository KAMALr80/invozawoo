<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class PurchaseObserver
{
    public function created(Purchase $purchase): void
    {
        $performerName = auth()->user()->name ?? 'System';
        $title = "New Purchase Order";
        $message = "Purchase from '{$purchase->supplier_name}' created by {$performerName}. Total: ₹" . number_format($purchase->grand_total, 2);
        $url = route('purchases.index');
        $icon = "fas fa-box-open";

        $recipients = User::whereIn('role', ['admin', 'staff'])->get();
        Notification::send($recipients, new SystemNotification($title, $message, $url, $icon));
    }
}
