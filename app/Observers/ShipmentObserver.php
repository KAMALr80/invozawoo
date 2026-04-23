<?php

namespace App\Observers;

use App\Models\Shipment;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class ShipmentObserver
{
    public function created(Shipment $shipment): void
    {
        $this->notify($shipment, "New Shipment Created");
    }

    public function updated(Shipment $shipment): void
    {
        if ($shipment->isDirty('status')) {
            $status = ucfirst(str_replace('_', ' ', $shipment->status));
            $this->notify($shipment, "Shipment Status Updated: {$status}");
        }
    }

    private function notify(Shipment $shipment, $title)
    {
        $performerName = auth()->user()->name ?? 'System';
        $message = "Shipment #{$shipment->shipment_number} for " . ($shipment->customer_name ?? 'Customer') . " - Performed by {$performerName}";
        $url = route('logistics.shipments.index');
        $icon = "fas fa-truck";

        $recipients = User::whereIn('role', ['admin', 'staff'])->get();
        Notification::send($recipients, new SystemNotification($title, $message, $url, $icon));
    }
}
