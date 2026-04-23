# Attendance & Leave Management System - Professional Integration Guide

## Overview

This guide documents the professional-level integration between Attendance and Leave management systems in the smartErp application. The system ensures seamless synchronization, proper validations, and comprehensive reporting.

## Architecture

### Models

#### **Leave Model** (`app/Models/Leave.php`)
- Manages all leave-related operations
- Key relationships:
  - `employee()` - Belongs to Employee
  - `attendances()` - Has many Attendance records created for leave period
  - `approver()`, `rejector()`, `canceller()` - User relationships for workflow
  - `leaveBalance()` - Related LeaveBalance record

- Key methods:
  - `hasSufficientBalance()` - Check if employee has enough leave balance
  - `hasConflicts()` - Check for overlapping leaves
  - `overlapsDate($date)` - Check if leave overlaps a specific date
  - `getDateRange()` - Get all dates in leave period
  - `getWorkingDaysAttribute()` - Get working days (excluding weekends & holidays)
  - `createLeaveAttendanceRecords()` - Auto-create attendance records
  - `deleteLeaveAttendanceRecords()` - Remove auto-created records
  - `updateLeaveBalance()` - Deduct from balance when approved
  - `reverseLeaveBalance()` - Restore balance when cancelled/rejected

#### **Attendance Model** (`app/Models/Attendance.php`)
- Manages attendance marking and tracking
- New constants:
  - `STATUS_ON_LEAVE` - For leave days
  - `STATUS_WEEKEND` - For weekends
  - `STATUS_HOLIDAY` - For holidays

- Key static methods:
  - `isEmployeeOnLeave($employeeId, $date)` - Check if employee is on leave
  - `getLeaveForDate($employeeId, $date)` - Get leave details for a date
  - `isHoliday($date)` - Check if date is a holiday
  - `isWeekend($date)` - Check if date is weekend
  - `autoMarkLeaveAttendance(Leave $leave)` - Auto-mark attendance when leave is approved
  - `removeLeaveAttendance(Leave $leave)` - Remove auto-marked records
  - `validateAgainstLeave()` - Validate before marking

### Services

#### **AttendanceService** (`app/Services/AttendanceService.php`)
Handles all attendance-related operations with leave integration.

**Key Methods:**
```php
// Mark attendance with validations
markAttendance(Employee $employee, array $data): Attendance

// Validate attendance against leave status
validateAttendanceAgainstLeave(int $employeeId, string $date): array

// Auto-mark attendance for approved leaves
autoMarkLeaveAttendance(Leave $leave): void

// Get attendance summary
getAttendanceSummary(Employee $employee, string $fromDate, string $toDate): array

// Get leaves summary
getLeavesSummary(Employee $employee, string $fromDate, string $toDate): Collection

// Calculate attendance percentage
calculateAttendancePercentage(Employee $employee, int $month, int $year): float
```

#### **LeaveService** (`app/Services/LeaveService.php`)
Handles all leave-related operations with attendance synchronization.

**Key Methods:**
```php
// Request new leave
requestLeave(Employee $employee, array $data): Leave

// Validate leave request
validateLeaveRequest(Employee $employee, array $data): array

// Approve leave with auto-attendance marking
approveLeave(Leave $leave, int $approvedBy, string $remarks = ''): void

// Reject leave
rejectLeave(Leave $leave, int $rejectedBy, string $reason = ''): void

// Cancel approved leave
cancelLeave(Leave $leave, int $cancelledBy, string $reason = ''): void

// Calculate leave days (excluding weekends & holidays)
calculateLeaveDays(string $fromDate, string $toDate, string $durationType = 'full_day'): int

// Check leave conflicts
hasLeaveConflicts(int $employeeId, Carbon $fromDate, Carbon $toDate): bool

// Check sufficient balance
hasSufficientBalance(int $employeeId, string $leaveType, float $days): bool

// Get employee leave summary
getEmployeeLeaveSummary(Employee $employee): array
```

### Events & Listeners

Events are dispatched when leaves change status:

- **LeaveApproved** → **LeaveApprovedListener**
  - Auto-marks attendance for approved leave dates
  - Excludes weekends and holidays
  
- **LeaveRejected** → **LeaveRejectedListener**
  - Removes auto-marked attendance records
  
- **LeaveCancelled** → **LeaveCancelledListener**
  - Removes auto-marked attendance records
  - Reverses leave balance deductions

## Usage Examples

### Example 1: Request Leave

```php
use App\Models\Employee;
use App\Services\LeaveService;

$employee = Employee::find(1);
$leaveService = new LeaveService();

$leaveData = [
    'leave_type' => 'annual',
    'duration_type' => 'full_day',
    'from_date' => '2026-05-01',
    'to_date' => '2026-05-05',
    'reason' => 'Vacation',
    'contact_number' => '03001234567',
];

try {
    $leave = $leaveService->requestLeave($employee, $leaveData);
    // Leave created with status 'Pending'
} catch (\Exception $e) {
    // Handle validation error
    dd($e->getMessage());
}
```

### Example 2: Approve Leave

```php
use App\Models\Leave;
use App\Services\LeaveService;

$leave = Leave::find(1);
$leaveService = new LeaveService();

try {
    // Approve leave
    $leaveService->approveLeave(
        $leave,
        auth()->user()->id, // approved_by
        'Approved by manager' // remarks
    );
    
    // This automatically:
    // 1. Updates leave status to 'Approved'
    // 2. Deducts from leave balance
    // 3. Creates attendance records for all leave dates
    // 4. Dispatches LeaveApproved event
} catch (\Exception $e) {
    dd($e->getMessage());
}
```

### Example 3: Mark Attendance

```php
use App\Models\Employee;
use App\Services\AttendanceService;

$employee = Employee::find(1);
$attendanceService = new AttendanceService();

$attendanceData = [
    'check_in' => '15:05:00',
    'check_out' => '23:55:00',
    'attendance_date' => now()->toDateString(),
];

try {
    $attendance = $attendanceService->markAttendance($employee, $attendanceData);
    // Attendance marked successfully
} catch (\Exception $e) {
    // If employee is on leave, error message:
    // "Employee is on approved Annual Leave leave. Cannot mark attendance."
    dd($e->getMessage());
}
```

### Example 4: Get Attendance Summary

```php
$attendanceService = new AttendanceService();

$summary = $attendanceService->getAttendanceSummary(
    $employee,
    '2026-04-01',
    '2026-04-30'
);

// Returns:
// [
//     'total_days' => 20,
//     'present' => 18,
//     'absent' => 1,
//     'late' => 1,
//     'half_day' => 0,
//     'on_leave' => 0,
//     'working_hours' => 162.5
// ]
```

### Example 5: Get Leave Summary

```php
$leaveService = new LeaveService();

$summary = $leaveService->getEmployeeLeaveSummary($employee);

// Returns: [
//     'annual' => [
//         'entitled' => 21,
//         'used' => 5,
//         'pending' => 2,
//         'remaining' => 14,
//         'carry_forward' => 0,
//         'total_available' => 21
//     ],
//     'sick' => [
//         'entitled' => 10,
//         'used' => 1,
//         'pending' => 0,
//         'remaining' => 9,
//         ...
//     ]
// ]
```

## Business Rules

1. **Leave Cannot Overlap**: Employee cannot have two approved leaves on same dates
2. **Minimum Notice Period**: As defined in LeavePolicy (default varies by leave type)
3. **Maximum Consecutive Days**: Maximum consecutive days limit defined in policy
4. **Balance Check**: Leave can only be approved if sufficient balance exists
5. **Auto-Attendance Marking**: When leave is approved, attendance is auto-marked as "On Leave"
6. **Exclude Weekends & Holidays**: Leave days calculation excludes weekends and holidays
7. **No Manual Attendance on Leave**: Cannot manually mark attendance for approved leave dates
8. **Balance Sync**: Leave balance is automatically updated when leave is approved/cancelled

## Database Operations

### Automatic Operations When Leave is Approved:
1. Update Leave.status = 'Approved'
2. Create Attendance records for each leave date (excluding weekends/holidays)
3. Update LeaveBalance (deduct from remaining)
4. Dispatch LeaveApproved event

### Automatic Operations When Leave is Rejected:
1. Update Leave.status = 'Rejected'
2. Delete auto-created Attendance records
3. Dispatch LeaveRejected event

### Automatic Operations When Leave is Cancelled:
1. Update Leave.status = 'Cancelled'
2. Delete auto-created Attendance records
3. Reverse LeaveBalance (add back to remaining)
4. Dispatch LeaveCancelled event

## Validation Rules

### Leave Request Validation:
- ✓ Required: from_date, to_date
- ✓ from_date <= to_date
- ✓ Cannot apply for past dates
- ✓ Minimum notice period respected
- ✓ No overlapping approved leaves
- ✓ Sufficient leave balance
- ✓ Within maximum consecutive days limit

### Attendance Marking Validation:
- ✓ Cannot mark on approved leave dates
- ✓ Cannot mark on weekends (if configured)
- ✓ Cannot mark on holidays
- ✓ No duplicate attendance for same date
- ✓ Valid check-in/check-out times

## Recommendations

1. **Use Services**: Always use AttendanceService and LeaveService for operations, not direct model methods
2. **Error Handling**: Wrap service calls in try-catch blocks
3. **Event Listeners**: Ensure event listeners are registered in EventServiceProvider
4. **Auditing**: Track all leave approvals/rejections/cancellations with user IDs
5. **Notifications**: Send notifications to employees when leave is approved/rejected
6. **Policy Enforcement**: Configure LeavePolicy for each leave type based on company rules
7. **Holiday Management**: Keep LeaveHoliday table updated with company holidays

## Configuration

### In `config/services.php`:
```php
'leave' => [
    'min_notice_days' => 1,
    'max_consecutive_days' => 30,
    'carry_forward_allowed' => true,
    'max_carry_forward_days' => 5,
],

'attendance' => [
    'work_start_time' => '15:00:00',
    'work_end_time' => '00:00:00',
    'required_working_hours' => 9,
    'grace_period_minutes' => 15,
],
```

## Testing

Create comprehensive tests for:
- Leave request validation
- Leave approval/rejection/cancellation
- Attendance marking validation
- Leave-Attendance sync
- Balance calculations
- Holiday and weekend exclusion
- Conflict detection

## Support & Maintenance

For issues or improvements:
1. Check error messages in service exceptions
2. Review event listener logs
3. Verify LeavePolicy configuration
4. Check LeaveBalance records
5. Review attendance validation rules

---

**Last Updated**: April 2026
**Version**: 1.0.0 - Professional Edition
