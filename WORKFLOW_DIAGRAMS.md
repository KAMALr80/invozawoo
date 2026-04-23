# Attendance & Leave Workflow Diagrams

## System Workflow - Leave Approval Process

```
┌─────────────────────────────────────────────────────────────────────┐
│                    EMPLOYEE REQUESTS LEAVE                          │
└─────────────────────────────────────────────────────────────────────┘
                                  │
                                  ↓
                    ┌─────────────────────────────┐
                    │   LeaveService.request()    │
                    │  ✓ Validation               │
                    │  ✓ Balance Check            │
                    │  ✓ Conflict Detection       │
                    │  ✓ Create Leave Record      │
                    └─────────────────────────────┘
                                  │
                    ┌─────────────┴────────────────┐
                    │                             │
                    ↓ (Valid)                     ↓ (Invalid)
          ┌──────────────────┐        ┌─────────────────────┐
          │ Leave Created:   │        │  Return Error       │
          │ Status: Pending  │        │  Message to User    │
          │ Status: Pending  │        └─────────────────────┘
          └──────────────────┘
                    │
                    ↓ (Sent to Manager)
        ┌──────────────────────────────────┐
        │    MANAGER REVIEWS LEAVE         │
        │  Approval / Rejection Option     │
        └──────────────────────────────────┘
                    │
        ┌───────────┴───────────┐
        │                       │
        ↓ (Approve)          ↓ (Reject)
┌───────────────────────┐ ┌──────────────────────┐
│ LeaveService.approve()│ │ LeaveService.reject()│
│                       │ │                      │
│ ✓ Update Status       │ │ ✓ Update Status      │
│ ✓ Deduct Balance      │ │ ✓ No Balance Change  │
│ ✓ Dispatch Event      │ │ ✓ Dispatch Event     │
│ ✓ Create Audit        │ │ ✓ Create Audit       │
└───────────────────────┘ └──────────────────────┘
        │                        │
        ↓                        ↓
┌──────────────────────┐   ┌───────────────────┐
│ LeaveApproved Event  │   │ LeaveRejected     │
│ Dispatched           │   │ Event Dispatched  │
└──────────────────────┘   └───────────────────┘
        │                        │
        ↓                        ↓
┌──────────────────────┐   ┌───────────────────┐
│ LeaveApprovedListener│   │ LeaveRejected     │
│                      │   │ Listener          │
│ • Auto-mark          │   │                   │
│   attendance for     │   │ • Remove any      │
│   leave dates        │   │   auto-marked     │
│ • Exclude weekends   │   │   records         │
│ • Exclude holidays   │   │                   │
│ • Create records     │   │                   │
│   as "On Leave"      │   │                   │
└──────────────────────┘   └───────────────────┘
        │                        │
        ↓                        ↓
    DATABASE                 DATABASE
Attendance records        No new records
created for dates        (leave just rejected)
```

---

## Attendance Marking Workflow

```
┌────────────────────────────────────────────────┐
│     EMPLOYEE MARKS ATTENDANCE                  │
│  (Check-in or Manual Marking)                  │
└────────────────────────────────────────────────┘
                      │
                      ↓
          ┌──────────────────────────┐
          │ AttendanceService.mark() │
          └──────────────────────────┘
                      │
                      ↓
      ┌───────────────────────────────────┐
      │ Validation Checks:                │
      │ ✓ Is employee on leave?           │
      │ ✓ Is it a weekend?                │
      │ ✓ Is it a holiday?                │
      │ ✓ Already marked today?           │
      └───────────────────────────────────┘
                      │
        ┌─────────────┴─────────────┐
        │                           │
   ✓ Valid                      ✗ Invalid
        │                           │
        ↓                           ↓
    ┌─────────┐            ┌───────────────┐
    │ Mark    │            │ Return Error  │
    │Attendance           │ Message       │
    │Record   │            │               │
    └─────────┘            │ Examples:     │
        │                  │ • On leave    │
        ↓                  │ • Weekend     │
    DATABASE               │ • Holiday     │
    Attendance             │ • Conflict    │
    Record Created      └───────────────────┘
    with Status: Present/Late
    Check-in/out times
    Calculated hours
```

---

## Leave Balance Synchronization Flow

```
┌──────────────────────────────────────────────┐
│         LEAVE APPROVED BY MANAGER            │
└──────────────────────────────────────────────┘
              │
              ↓
┌──────────────────────────────────────────────┐
│ LeaveBalance Table (Before):                 │
│ Employee: John Doe                           │
│ Leave Type: Annual                           │
│ Year: 2026                                   │
│                                              │
│ Entitled:   21 days                          │
│ Used:       5 days  ← Will increase          │
│ Remaining:  14 days ← Will decrease          │
│ Pending:    2 days                           │
│ Total Avail: 21 days                         │
└──────────────────────────────────────────────┘
              │
              ↓
    ┌──────────────────────────┐
    │ Leave Days Calculation:  │
    │ From: 2026-05-01        │
    │ To:   2026-05-05        │
    │ Duration: Full Day       │
    │                          │
    │ Exclude:                │
    │ • Weekends: -1 (Sat)    │
    │ • Holidays: 0            │
    │                          │
    │ Total Days: 4 days      │
    └──────────────────────────┘
              │
              ↓
┌──────────────────────────────────────────────┐
│ DEDUCT FROM BALANCE:                         │
│                                              │
│ New Used:       5 + 4 = 9 days               │
│ New Remaining:  14 - 4 = 10 days             │
│ New Total Avail: 21 days                     │
│                                              │
│ leave_balance_before: 14                     │
│ leave_balance_after:  10                     │
└──────────────────────────────────────────────┘
              │
              ↓
┌──────────────────────────────────────────────┐
│ LeaveBalance Table (After):                  │
│ Employee: John Doe                           │
│ Leave Type: Annual                           │
│ Year: 2026                                   │
│                                              │
│ Entitled:   21 days ✓                        │
│ Used:       9 days  ✓ (was 5)                │
│ Remaining:  10 days ✓ (was 14)               │
│ Pending:    2 days  ✓                        │
│ Total Avail: 21 days ✓                       │
└──────────────────────────────────────────────┘
```

---

## Data Integrity Matrix

```
┌────────────────────────────────────────────────────────────┐
│         STATE          │  ATTENDANCE  │  LEAVE_BALANCE     │
├────────────────────────────────────────────────────────────┤
│ Leave: Pending         │  None        │  Unchanged         │
├────────────────────────────────────────────────────────────┤
│ Leave: Approved        │  ✓ Created   │  ✓ Deducted        │
│                        │  (On Leave)  │  (Used +)          │
├────────────────────────────────────────────────────────────┤
│ Leave: Rejected        │  None        │  Unchanged         │
├────────────────────────────────────────────────────────────┤
│ Leave: Cancelled       │  ✓ Deleted   │  ✓ Reversed        │
│                        │  (if auto)   │  (Used -)          │
├────────────────────────────────────────────────────────────┤
│ Manual Attendance      │  ✓ Can mark  │  No change         │
│ (On non-leave date)    │  if not      │                    │
│                        │  on leave    │                    │
├────────────────────────────────────────────────────────────┤
│ Manual Attendance      │  ✗ Blocked   │  No change         │
│ (On leave date)        │  Error!      │                    │
├────────────────────────────────────────────────────────────┤
│ Check-in on Leave      │  ✗ Blocked   │  No change         │
│ (Real-time)            │  at device   │                    │
└────────────────────────────────────────────────────────────┘
```

---

## Validation Decision Tree

```
                    ┌─────────────────────────────────┐
                    │  MARK ATTENDANCE REQUEST        │
                    └─────────────────────────────────┘
                                 │
                                 ↓
                    ┌────────────────────────────────┐
                    │  Is employee on approved leave?│
                    └────────────────────────────────┘
                           │              │
                         YES              NO
                           │              │
                           ↓              ↓
                    ┌──────────┐  ┌──────────────────┐
                    │ BLOCKED  │  │  Is it weekend?  │
                    │ Error:   │  └──────────────────┘
                    │ On Leave │         │      │
                    └──────────┘        YES     NO
                                        │      │
                                        ↓      ↓
                                   ┌────┐  ┌──────────┐
                                   │BLKD│  │Is holiday?
                                   │Err │  └──────────┘
                                   └────┘        │      │
                                               YES     NO
                                                │      │
                                                ↓      ↓
                                           ┌────┐  ┌─────────────┐
                                           │BLKD│  │Attendance   │
                                           │Err │  │exists today?│
                                           └────┘  └─────────────┘
                                                       │       │
                                                      YES      NO
                                                       │       │
                                                       ↓       ↓
                                                  ┌────┐   ┌────────┐
                                                  │BLKD│   │✓ ALLOW │
                                                  │Err │   │Mark    │
                                                  └────┘   │Now     │
                                                           └────────┘
```

---

## Service Interaction Diagram

```
┌──────────────────────────────────────────────────────────────────┐
│                      CONTROLLER LAYER                            │
│  (Attendance/Leave Controllers receive requests)                 │
└──────────────────────────────────────────────────────────────────┘
          │                                              │
          ↓                                              ↓
┌─────────────────────────────┐        ┌─────────────────────────────┐
│   AttendanceService         │        │    LeaveService             │
│                             │        │                             │
│ Methods:                    │        │ Methods:                    │
│ • markAttendance()          │        │ • requestLeave()            │
│ • validate()                │        │ • validateRequest()         │
│ • getAutoMarkLeave()        │        │ • approveLeave()            │
│ • removeLeaveAttendance()   │        │ • rejectLeave()             │
│ • getSummary()              │        │ • cancelLeave()             │
│ • getPercentage()           │        │ • calculateDays()           │
│                             │        │ • checkBalance()            │
│                             │        │ • deductBalance()           │
│ Uses:                       │        │ • reverseBalance()          │
│ • Attendance Model          │        │ • getSummary()              │
│ • Leave Model               │        │                             │
│ • Holiday Model             │        │ Uses:                       │
│ • AttendanceService events  │        │ • Leave Model               │
│                             │        │ • LeaveBalance Model        │
└─────────────────────────────┘        │ • LeavePolicy Model         │
          │                             │ • AttendanceService         │
          │                             │                             │
          └─────────────┬───────────────┘
                        ↓
            ┌───────────────────────────┐
            │      Model Layer          │
            │                           │
            │ • Leave                   │
            │ • Attendance              │
            │ • LeaveBalance            │
            │ • LeavePolicy             │
            │ • LeaveHoliday            │
            │ • Employee                │
            └───────────────────────────┘
                        │
        ┌───────────────┼───────────────┐
        ↓               ↓               ↓
    ┌────────┐    ┌──────────┐    ┌──────────┐
    │ Events │    │ Database │    │ Listeners│
    │        │    │          │    │          │
    │ • Appr │    │Attendances   │• Auto Mark
    │ • Rjct │    │Leaves    │    │• Cleanup │
    │ • Canc │    │Balances  │    │• Sync    │
    └────────┘    └──────────┘    └──────────┘
```

---

## Calendar View - Employee Status

```
┌─────────────────────────────────────────────────────────────┐
│                        MAY 2026                             │
├─────────┬────────┬────────┬────────┬────────┬────────┬────────┤
│   SUN   │  MON   │  TUE   │  WED   │  THU   │  FRI   │  SAT   │
├─────────┼────────┼────────┼────────┼────────┼────────┼────────┤
│    3    │   4    │   5    │   6    │   7    │   8    │   9    │
│         │✓ PRES  │✓ PRES  │✓ PRES  │✓ PRES  │✓ PRES  │        │
│         │9h 15m  │9h 10m  │9h 20m  │9h 05m  │9h 30m  │        │
├─────────┼────────┼────────┼────────┼────────┼────────┼────────┤
│   10    │   11   │   12   │   13   │   14   │   15   │   16   │
│         │✓ PRES  │ 🏖 LEAVE│ 🏖 LEAVE│ 🏖 LEAVE│ 🏖 LEAVE│ 🏖 LEAVE│
│         │9h 08m  │Annual  │Annual  │Annual  │Annual  │Annual  │
│         │        │(Auto)  │(Auto)  │(Auto)  │(Auto)  │        │
├─────────┼────────┼────────┼────────┼────────┼────────┼────────┤
│   17    │   18   │   19   │   20   │   21   │   22   │   23   │
│         │🏖LEAVE │✓ PRES  │⏰ LATE  │ ✓PRES  │✗ ABS   │        │
│         │(Auto)  │9h 12m  │8h 50m  │9h 15m  │-       │        │
├─────────┼────────┼────────┼────────┼────────┼────────┼────────┤
│   24    │   25   │   26   │   27   │   28   │   29   │   30   │
│         │✓ PRES  │✓ PRES  │🏖LEAVE │🏖LEAVE │✓ PRES  │        │
│         │9h 10m  │9h 25m  │(Auto)  │(Auto)  │9h 18m  │        │
├─────────┴────────┴────────┴────────┴────────┴────────┴────────┤
│                                                                │
│  Legend:                                                      │
│  ✓ PRES   = Present with working hours                       │
│  ⏰ LATE   = Late check-in                                    │
│  ✗ ABS    = Absent                                            │
│  🏖 LEAVE  = On leave (auto-marked)                           │
│  (Auto)   = Auto-marked by system                            │
│                                                                │
│  Monthly Summary:                                             │
│  • Total Working Days: 20                                     │
│  • Present: 14                                                │
│  • On Leave: 5                                                │
│  • Absent: 1                                                  │
│  • Attendance: 70%                                            │
│  • Total Hours: 129h 45m                                      │
│                                                                │
│  Leave Balance (Annual):                                      │
│  • Entitled: 21 days                                          │
│  • Used: 5 days (this month: 5)                               │
│  • Remaining: 16 days                                         │
│  • Pending: 0 days                                            │
└────────────────────────────────────────────────────────────────┘
```

---

**Version**: 1.0.0  
**Last Updated**: April 17, 2026  
**Status**: ✅ Production Ready
