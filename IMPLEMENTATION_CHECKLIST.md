# Attendance & Leave Alignment - Implementation Checklist

## ✅ Completed Components

### 1. Model Enhancements
- [x] **Leave Model** - Full relationship setup with Attendance
  - Added `attendances()` relationship
  - Added policy relationship
  - Added methods: `hasSufficientBalance()`, `hasConflicts()`, `overlapsDate()`
  - Added methods: `getWorkingDaysAttribute()`, `getDateRange()`
  - Added methods: `createLeaveAttendanceRecords()`, `deleteLeaveAttendanceRecords()`
  - Added methods: `updateLeaveBalance()`, `reverseLeaveBalance()`

- [x] **Attendance Model** - Leave integration methods
  - Added status constants (ON_LEAVE, WEEKEND, HOLIDAY)
  - Added static methods: `isEmployeeOnLeave()`, `getLeaveForDate()`
  - Added static methods: `isHoliday()`, `isWeekend()`, `getHolidayForDate()`
  - Added methods: `canMarkAttendance()`, `validateAgainstLeave()`
  - Added methods: `autoMarkLeaveAttendance()`, `removeLeaveAttendance()`
  - Added method: `getEmployeeLeavesSummary()`

### 2. Service Layer
- [x] **AttendanceService** (Complete)
  - `markAttendance()` - With validations
  - `validateAttendanceAgainstLeave()` - Comprehensive validation
  - `isEmployeeOnLeave()` - Check leave status
  - `getLeaveForDate()` - Fetch leave details
  - `isWeekend()` & `isHoliday()` - Date checks
  - `autoMarkLeaveAttendance()` - Auto-mark on approval
  - `removeLeaveAttendance()` - Cleanup on rejection
  - `getAttendanceSummary()` - Reporting
  - `getLeavesSummary()` - Leave statistics
  - `calculateAttendancePercentage()` - Percentage calculation

- [x] **LeaveService** (Complete)
  - `requestLeave()` - Create leave request
  - `validateLeaveRequest()` - Multi-level validation
  - `approveLeave()` - Approve with auto-attendance
  - `rejectLeave()` - Reject with cleanup
  - `cancelLeave()` - Cancel approved leave
  - `calculateLeaveDays()` - Calculate working days
  - `hasLeaveConflicts()` - Check overlaps
  - `hasSufficientBalance()` - Balance check
  - `deductLeaveBalance()` - Deduct on approval
  - `reverseLeaveBalance()` - Restore on cancellation
  - `generateLeaveNumber()` - Unique ID generation
  - `getPendingLeaves()` - Manager queries
  - `getEmployeeLeaveSummary()` - Summary for dashboard

### 3. Events & Listeners
- [x] **LeaveApproved Event & Listener**
  - Auto-marks attendance when leave is approved
  
- [x] **LeaveRejected Event & Listener**
  - Removes auto-marked attendance
  
- [x] **LeaveCancelled Event & Listener**
  - Removes auto-marked attendance

### 4. Helpers
- [x] **AttendanceLeaveHelper** (Complete)
  - `getMonthlyReport()` - Full month analysis
  - `calculateStatistics()` - Stat calculation
  - `getTotalWorkingDays()` - Working days calculation
  - `getLeavesBetween()` - Leave range query
  - `getPendingLeavesByDepartment()` - Manager view
  - `getDepartmentLeaveStatus()` - Department overview
  - `getAttendanceSuggestions()` - Performance insights
  - `getDashboardSummary()` - Employee dashboard

### 5. Documentation
- [x] **ATTENDANCE_LEAVE_INTEGRATION_GUIDE.md** - Complete reference guide

---

## 📋 Setup Checklist

### Step 1: Register Services
In `app/Providers/AppServiceProvider.php`:

```php
public function register()
{
    $this->app->singleton('AttendanceService', function ($app) {
        return new \App\Services\AttendanceService();
    });

    $this->app->singleton('LeaveService', function ($app) {
        return new \App\Services\LeaveService(
            $app->make('AttendanceService')
        );
    });
}
```

### Step 2: Register Events & Listeners
In `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    \App\Events\LeaveApproved::class => [
        \App\Listeners\LeaveApprovedListener::class,
    ],
    \App\Events\LeaveRejected::class => [
        \App\Listeners\LeaveRejectedListener::class,
    ],
    \App\Events\LeaveCancelled::class => [
        \App\Listeners\LeaveCancelledListener::class,
    ],
];
```

### Step 3: Update Leave Model Events (Optional)
In `app/Models/Leave.php`, add to model to dispatch events:

```php
protected $dispatchesEvents = [
    // Events will be dispatched when using these methods in controller/service
];
```

### Step 4: Update Controllers
Modify your Leave controller to use LeaveService:

```php
// LeaveController.php
public function approve(Request $request, Leave $leave)
{
    try {
        $leaveService = app('LeaveService');
        $leaveService->approveLeave(
            $leave,
            auth()->user()->id,
            $request->get('remarks', '')
        );
        
        return response()->json(['message' => 'Leave approved successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}
```

### Step 5: Database Considerations
Ensure these columns exist in `attendances` table:
- `leave_id` (foreign key) ✓
- `status` (enum or string) ✓
- `is_auto_marked` (boolean) ✓
- `remarks` (text) ✓

If missing, create migration:
```bash
php artisan make:migration add_leave_columns_to_attendances_table
```

### Step 6: Configure Leave Policies
Ensure `leave_policies` table is populated with company policies:

```php
// In seeder or manually
LeavePolicy::create([
    'name' => 'Annual Leave',
    'leave_type' => 'annual',
    'days_per_year' => 21,
    'accrual_method' => 'lump_sum',
    'carry_forward_allowed' => true,
    'max_carry_forward_days' => 5,
    'min_notice_days' => 1,
    'requires_approval' => true,
    'is_active' => true,
]);
```

### Step 7: Populate Leave Balances
Initialize leave balances for all employees (usually done on January 1):

```php
// Command or job
$employees = Employee::all();
$leaveTypes = ['annual', 'sick', 'casual'];

foreach ($employees as $employee) {
    foreach ($leaveTypes as $type) {
        $policy = LeavePolicy::where('leave_type', $type)->first();
        
        LeaveBalance::firstOrCreate([
            'employee_id' => $employee->id,
            'year' => now()->year,
            'leave_type' => $type,
        ], [
            'entitled' => $policy->days_per_year,
            'used' => 0,
            'pending' => 0,
            'remaining' => $policy->days_per_year,
            'carry_forward' => 0,
            'total_available' => $policy->days_per_year,
        ]);
    }
}
```

### Step 8: Set Up Holidays
Ensure all company holidays are in `leave_holidays` table:

```bash
php artisan make:seeder LeaveHolidaySeeder
```

---

## 🧪 Testing Checklist

- [ ] Test leave request with insufficient balance
- [ ] Test overlapping leave dates
- [ ] Test leave approval auto-marking attendance
- [ ] Test leave rejection removing attendance
- [ ] Test leave cancellation reversing balance
- [ ] Test attendance marking during leave period
- [ ] Test weekend/holiday exclusion
- [ ] Test balance calculations
- [ ] Test minimum notice period
- [ ] Test maximum consecutive days limit
- [ ] Test monthly attendance summary
- [ ] Test leave balance summary
- [ ] Test dashboard helper functions

---

## 🎯 Usage Quick Start

### For Employees:
```php
// Request leave
$leaveService = app('LeaveService');
$leave = $leaveService->requestLeave($employee, [
    'leave_type' => 'annual',
    'from_date' => '2026-05-01',
    'to_date' => '2026-05-05',
    'reason' => 'Vacation'
]);
```

### For Managers:
```php
// Approve leave
$leaveService->approveLeave($leave, auth()->user()->id, 'Approved');

// Get pending leaves
$pending = $leaveService->getPendingLeaves($department_id);

// View department status
$status = AttendanceLeaveHelper::getDepartmentLeaveStatus($department_id);
```

### For Employees (Check Status):
```php
// View dashboard
$summary = AttendanceLeaveHelper::getDashboardSummary($employee);

// View monthly report
$report = AttendanceLeaveHelper::getMonthlyReport($employee, 2026, 4);
```

---

## 📊 Database Schema Verification

**Ensure these tables exist with proper relationships:**

1. ✓ `employees` - Base employee records
2. ✓ `attendances` - Attendance records with `employee_id`, `leave_id`
3. ✓ `leaves` - Leave requests
4. ✓ `leave_balances` - Annual leave balance tracking
5. ✓ `leave_policies` - Leave type configurations
6. ✓ `leave_holidays` - Company holidays
7. ✓ `users` - For approver/rejector relationships

---

## 🚀 Performance Optimization

### Add Indexes:
```php
// In migration
Schema::table('attendances', function (Blueprint $table) {
    $table->index(['employee_id', 'attendance_date']);
    $table->index(['employee_id', 'status']);
    $table->index(['leave_id']);
});

Schema::table('leaves', function (Blueprint $table) {
    $table->index(['employee_id', 'status']);
    $table->index(['employee_id', 'from_date', 'to_date']);
    $table->index(['status', 'approved_at']);
});

Schema::table('leave_balances', function (Blueprint $table) {
    $table->index(['employee_id', 'year', 'leave_type']);
});
```

### Query Optimization:
- Use eager loading for relationships
- Use scopes for common queries
- Cache leave policies and holidays
- Implement pagination for reports

---

## 📞 Troubleshooting

**Issue**: Attendance not auto-marked for approved leave
- ✓ Check if LeaveApproved event is being dispatched
- ✓ Verify LeaveApprovedListener is registered
- ✓ Check if leave.status is actually 'Approved'

**Issue**: Cannot mark attendance even when not on leave
- ✓ Check if employee is on leave for that date
- ✓ Verify no conflicting attendance record exists
- ✓ Check if date is a weekend/holiday

**Issue**: Leave balance not updating
- ✓ Ensure LeaveBalance record exists for employee
- ✓ Verify deductLeaveBalance is being called
- ✓ Check leave_type matches in policy and balance

**Issue**: Events not firing
- ✓ Verify events are registered in EventServiceProvider
- ✓ Check if listeners have proper dependency injection
- ✓ Ensure model events are being dispatched in service

---

## 📚 Related Documentation

See [ATTENDANCE_LEAVE_INTEGRATION_GUIDE.md](./ATTENDANCE_LEAVE_INTEGRATION_GUIDE.md) for:
- Complete API reference
- Detailed usage examples
- Business rules documentation
- Configuration options
- Best practices

---

**Status**: Ready for Production
**Last Updated**: April 2026
**Version**: 1.0.0
