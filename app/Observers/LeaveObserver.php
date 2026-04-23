<?php

namespace App\Observers;

use App\Models\Leave;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class LeaveObserver
{
    public function created(Leave $leave): void
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'hr')->get();
        
        $employeeName = $leave->employee ? $leave->employee->name : 'Staff';
        $title = "New Leave Application";
        $message = "{$employeeName} has applied for leave from " . date('d M', strtotime($leave->start_date)) . " to " . date('d M', strtotime($leave->end_date));
        $url = route('leaves.index');
        $icon = "fas fa-calendar-day";

        Notification::send($admins, new SystemNotification($title, $message, $url, $icon));
    }
}
