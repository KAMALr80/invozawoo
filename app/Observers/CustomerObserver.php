<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        $performerName = auth()->user()->name ?? 'System';
        $title = "New Customer Registered";
        $message = "Customer '{$customer->name}' added by {$performerName}.";
        $url = route('customers.index');
        $icon = "fas fa-user-plus";

        $recipients = User::whereIn('role', ['admin', 'staff'])->get();
        Notification::send($recipients, new SystemNotification($title, $message, $url, $icon));
    }
}
