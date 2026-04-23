# Attendance & Leave - Quick Reference Guide

## 🔍 Common Queries

### Check if employee is on leave
```php
use App\Models\Attendance;

$isOnLeave = Attendance::isEmployeeOnLeave($employeeId, '2026-05-01');
```

### Get leave for a specific date
```php
$leave = Attendance::getLeaveForDate($employeeId, '2026-05-01');
if ($leave) {
    echo $leave->leave_type_label; // "Annual Leave"
}
```

### Check if date is holiday
```php
if (Attendance::isHoliday('2026-04-23')) {
    echo "It's a holiday";
}
```

### Check if date is weekend
```php
if (Attendance::isWeekend('2026-04-26')) { // Saturday
    echo "It's weekend";
}
```

---

## ✅ Common Operations

### Mark Attendance
```php
use App\Services\AttendanceService;

$attendanceService = app('AttendanceService');

try {
    $attendance = $attendanceService->markAttendance($employee, [
        'check_in' => '15:05:00',
        'check_out' => '23:55:00',
        'attendance_date' => now()->toDateString(),
    ]);
} catch (\Exception $e) {
    // Handle error
    dd($e->getMessage());
}
```

### Request Leave
```php
use App\Services\LeaveService;

$leaveService = app('LeaveService');

try {
    $leave = $leaveService->requestLeave($employee, [
        'leave_type' => 'annual',
        'duration_type' => 'full_day',
        'from_date' => '2026-05-01',
        'to_date' => '2026-05-05',
        'reason' => 'Family vacation',
    ]);
} catch (\Exception $e) {
    dd($e->getMessage());
}
```

### Approve Leave
```php
$leaveService = app('LeaveService');

try {
    $leaveService->approveLeave($leave, auth()->user()->id, 'Approved');
    // Automatically:
    // 1. Updates status to 'Approved'
    // 2. Deducts leave balance
    // 3. Creates attendance records
} catch (\Exception $e) {
    dd($e->getMessage());
}
```

### Reject Leave
```php
$leaveService->rejectLeave($leave, auth()->user()->id, 'Denied due to project deadline');
// Automatically removes auto-marked attendance
```

### Cancel Approved Leave
```php
$leaveService->cancelLeave($leave, auth()->user()->id, 'Change of plans');
// Automatically reverses balance and removes attendance
```

---

## 📊 Common Reports

### Monthly Attendance Summary
```php
use App\Helpers\AttendanceLeaveHelper;

$report = AttendanceLeaveHelper::getMonthlyReport($employee, 2026, 4);

echo "Presents: " . $report['statistics']['presents'];
echo "Absents: " . $report['statistics']['absents'];
echo "On Leave: " . $report['statistics']['on_leaves'];
echo "Attendance %: " . $report['statistics']['attendance_percentage'];
```

### Employee Dashboard
```php
$summary = AttendanceLeaveHelper::getDashboardSummary($employee);

echo "This month presents: " . $summary['this_month_presents'];
echo "This month leaves: " . $summary['this_month_on_leave'];
echo "Total remaining leaves: " . $summary['total_remaining_leaves'];
```

### Department Leave Status
```php
$status = AttendanceLeaveHelper::getDepartmentLeaveStatus($departmentId);

foreach ($status as $item) {
    echo $item['employee_name'];
    echo $item['leave_balance']; // By leave type
}
```

### Pending Approval Leaves
```php
$pending = AttendanceLeaveHelper::getPendingLeavesByDepartment('Engineering');

foreach ($pending as $leave) {
    echo $leave->employee->name;
    echo $leave->from_date . ' to ' . $leave->to_date;
}
```

### Attendance Improvement Suggestions
```php
$suggestions = AttendanceLeaveHelper::getAttendanceSuggestions($employee, 4, 2026);

foreach ($suggestions as $suggestion) {
    echo $suggestion;
}
```

---

## 🔐 Validation Examples

### Validate Leave Request
```php
$leaveService = app('LeaveService');

$validation = $leaveService->validateLeaveRequest($employee, [
    'from_date' => '2026-05-01',
    'to_date' => '2026-05-05',
    'leave_type' => 'annual',
]);

if (!$validation['valid']) {
    echo $validation['message'];
}
```

### Validate Attendance Marking
```php
$attendanceService = app('AttendanceService');

$validation = $attendanceService->validateAttendanceAgainstLeave(
    $employeeId,
    '2026-05-01'
);

if (!$validation['valid']) {
    echo $validation['message'];
}
```

### Check Leave Conflicts
```php
$has_conflicts = $leaveService->hasLeaveConflicts(
    $employeeId,
    Carbon::parse('2026-05-01'),
    Carbon::parse('2026-05-05')
);
```

### Check Leave Balance
```php
$hasSufficient = $leaveService->hasSufficientBalance(
    $employeeId,
    'annual',  // leave_type
    5          // days needed
);
```

---

## 🎯 Blade Template Examples

### Employee Leave Balance Table
```blade
@php
    $summary = AttendanceLeaveHelper::getDashboardSummary($employee);
@endphp

<table class="table">
    <tr>
        <th>Leave Type</th>
        <th>Entitled</th>
        <th>Used</th>
        <th>Remaining</th>
    </tr>
    @forelse($summary['leave_balances'] as $type => $balance)
        <tr>
            <td>{{ ucfirst($type) }}</td>
            <td>{{ $balance['entitled'] }}</td>
            <td>{{ $balance['used'] }}</td>
            <td>{{ $balance['remaining'] }}</td>
        </tr>
    @empty
        <tr><td colspan="4">No leave balance data</td></tr>
    @endforelse
</table>
```

### Attendance Status Display
```blade
@foreach($attendances as $attendance)
    <div>
        <span class="badge {{ $attendance->status_badge_class }}">
            {{ $attendance->status_with_emoji }}
        </span>
        <span>{{ $attendance->formatted_net_working_hours }}</span>
    </div>
@endforeach
```

### Pending Leave Approvals
```blade
@php
    $pending = AttendanceLeaveHelper::getPendingLeavesByDepartment('Engineering');
@endphp

<div class="pending-leaves">
    @forelse($pending as $leave)
        <div class="leave-card">
            <h5>{{ $leave->employee->name }}</h5>
            <p>{{ $leave->leave_type_label }}</p>
            <p>{{ $leave->formatted_from_date }} to {{ $leave->formatted_to_date }}</p>
            <button onclick="approve({{ $leave->id }})">Approve</button>
            <button onclick="reject({{ $leave->id }})">Reject</button>
        </div>
    @empty
        <p>No pending leaves</p>
    @endforelse
</div>
```

---

## 🛠️ Troubleshooting Commands

### Check Leave Balance for Employee
```php
// In Tinker
$employee = App\Models\Employee::find(1);
$employee->leaveBalances()->where('year', 2026)->get();
```

### View All Pending Leaves
```php
App\Models\Leave::where('status', 'Pending')->get();
```

### Check Attendance on Specific Date
```php
App\Models\Attendance::where('employee_id', 1)
    ->where('attendance_date', '2026-05-01')
    ->first();
```

### View All Auto-Marked Attendances
```php
App\Models\Attendance::where('is_auto_marked', true)
    ->where('status', 'On Leave')
    ->get();
```

### Recalculate Leave Balances
```php
$year = 2026;
$employees = App\Models\Employee::all();

foreach ($employees as $employee) {
    $balances = $employee->leaveBalances()->where('year', $year)->get();
    
    foreach ($balances as $balance) {
        $balance->calculateTotals();
    }
}
```

---

## 📅 Date Calculations

### Calculate Working Days Between Dates
```php
use App\Helpers\AttendanceLeaveHelper;

$workingDays = AttendanceLeaveHelper::getTotalWorkingDays(
    Carbon::parse('2026-05-01'),
    Carbon::parse('2026-05-31')
);

echo "Working days in May: $workingDays";
```

### Calculate Leave Days
```php
$leaveService = app('LeaveService');

$days = $leaveService->calculateLeaveDays(
    '2026-05-01',
    '2026-05-05',
    'full_day'
);

echo "Leave days: $days";
```

---

## 💾 Database Maintenance

### Clean Up Old Attendance Records
```php
// Keep last 2 years
$keepFrom = now()->subYears(2);

App\Models\Attendance::where('created_at', '<', $keepFrom)->delete();
```

### Archive Old Leave Records
```php
App\Models\Leave::where('created_at', '<', now()->subYears(1))
    ->update(['archived' => true]);
```

### Verify Leave-Attendance Sync
```php
// Find mismatched records
$leaves = App\Models\Leave::where('status', 'Approved')->get();

foreach ($leaves as $leave) {
    $attendance_count = $leave->attendances()->count();
    $expected_days = count($leave->getDateRange());
    
    if ($attendance_count !== $expected_days) {
        echo "Mismatch for leave {$leave->id}";
    }
}
```

---

## 🔔 Event Triggering

### Manually Dispatch Leave Approval Event
```php
use App\Events\LeaveApproved;

event(new LeaveApproved($leave));
// This will trigger auto-attendance marking
```

### Manually Dispatch Leave Rejection Event
```php
use App\Events\LeaveRejected;

event(new LeaveRejected($leave));
// This will remove auto-marked attendance
```

### Manually Dispatch Leave Cancellation Event
```php
use App\Events\LeaveCancelled;

event(new LeaveCancelled($leave));
// This will remove auto-marked attendance and reverse balance
```

---

## 🎓 Best Practices

✅ **DO**:
- Use services for all operations
- Validate before processing
- Handle exceptions gracefully
- Log all approvals/rejections
- Keep leave policies updated
- Test with various scenarios
- Cache frequently accessed data
- Use transactions for multi-step operations

❌ **DON'T**:
- Directly modify attendance/leave records
- Skip validations
- Mix manual and auto-marking
- Ignore error messages
- Assume data consistency
- Update balance without sync
- Create duplicate attendance records

---

**Last Updated**: April 2026
**Version**: 1.0.0
