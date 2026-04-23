<?php

namespace App\Listeners;

use App\Events\LeaveApproved;
use App\Services\AttendanceService;

class LeaveApprovedListener
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Handle the event
     */
    public function handle(LeaveApproved $event): void
    {
        // Auto-mark attendance for approved leave
        $this->attendanceService->autoMarkLeaveAttendance($event->leave);
    }
}
