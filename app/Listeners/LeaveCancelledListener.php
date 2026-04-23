<?php

namespace App\Listeners;

use App\Events\LeaveCancelled;
use App\Services\AttendanceService;

class LeaveCancelledListener
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Handle the event
     */
    public function handle(LeaveCancelled $event): void
    {
        // Remove auto-marked attendance for cancelled leave
        $this->attendanceService->removeLeaveAttendance($event->leave);
    }
}
