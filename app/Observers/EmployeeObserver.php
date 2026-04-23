<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class EmployeeObserver
{
    public function created(Employee $employee): void
    {
        $performerName = auth()->user()->name ?? 'System';
        $title = "New Employee Onboarded";
        $dept = $employee->department->name ?? 'system';
        $message = "'{$employee->name}' added to {$dept} by {$performerName}.";
        $url = route('employees.index');
        $icon = "fas fa-user-tie";

        $recipients = User::whereIn('role', ['admin', 'hr', 'staff'])->get();
        Notification::send($recipients, new SystemNotification($title, $message, $url, $icon));
    }
}
