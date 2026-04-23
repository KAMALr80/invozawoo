<?php

namespace App\Listeners;

use App\Events\LeaveRejected;
use App\Services\AttendanceService;

class LeaveRejectedListener
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Handle the event
     */
    public function handle(LeaveRejected $event): void
    {
        // Remove auto-marked attendance for rejected leave
        $this->attendanceService->removeLeaveAttendance($event->leave);
    }
}
