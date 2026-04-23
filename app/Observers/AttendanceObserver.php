<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        $performerName = auth()->user()->name ?? 'System';
        $employeeName = $attendance->employee->name ?? 'Staff';
        $status = ucfirst($attendance->status);
        
        $title = "Attendance Marked: {$status}";
        $message = "{$employeeName} marked as {$status} by {$performerName}.";
        $url = route('attendance.manage');
        $icon = "fas fa-clock";

        $recipients = User::whereIn('role', ['admin', 'hr'])->get();
        Notification::send($recipients, new SystemNotification($title, $message, $url, $icon));
    }
}
