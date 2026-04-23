<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /* ================= STAFF ================= */

    public function myAttendance()
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('attendance_date', today())
            ->first();

        $history = Attendance::where('employee_id', $employee->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(10);

        // Calculate statistics
        $stats = [
            'present_days' => Attendance::where('employee_id', $employee->id)
                ->where(function($query) {
                    $query->where('status', 'present')
                          ->orWhere('status', 'Present');
                })
                ->count(),
            'late_days' => Attendance::where('employee_id', $employee->id)
                ->where(function($query) {
                    $query->where('status', 'late')
                          ->orWhere('status', 'Late');
                })
                ->count(),
            'absent_days' => Attendance::where('employee_id', $employee->id)
                ->where(function($query) {
                    $query->where('status', 'absent')
                          ->orWhere('status', 'Absent');
                })
                ->count(),
            'half_days' => Attendance::where('employee_id', $employee->id)
                ->where('status', 'Half Day')
                ->count(),
        ];

        return view('attendance.my', compact(
            'employee',
            'todayAttendance',
            'history',
            'stats'
        ));
    }

   public function checkIn()
{
    $employee = Employee::where('user_id', Auth::id())->firstOrFail();
    $now = now();

    $officeStart = Carbon::createFromTime(15, 0);   // 3:00 PM
    $lateTime = Carbon::createFromTime(15, 15);      // 3:15 PM
    $status = 'Present';
    $remarks = 'On time - Checked in at ' . $now->format('h:i A');

    if ($now->gt($lateTime)) {
        $status = 'Late';
        $remarks = 'Late check-in at ' . $now->format('h:i A');
    }

    $attendance = Attendance::updateOrCreate(
        [
            'employee_id' => $employee->id,
            'attendance_date' => today(),
        ],
        [
            'check_in' => $now->format('H:i:s'),
            'status' => $status,
            'remarks' => $remarks,
            'marked_by' => Auth::id(),
            'is_auto_marked' => false
        ]
    );

    Log::info('Employee checked in', [
        'employee_id' => $employee->id,
        'time' => $now->format('H:i:s'),
        'status' => $status
    ]);

    $message = $status === 'Late'
        ? "⚠️ Late check-in at {$now->format('h:i A')} (After 3:15 PM)"
        : "✅ Check-in successful at {$now->format('h:i A')}";

    return back()->with('success', $message);
}

   public function checkOut()
{
    $employee = Employee::where('user_id', Auth::id())->firstOrFail();

    $attendance = Attendance::where('employee_id', $employee->id)
        ->where('attendance_date', today())
        ->firstOrFail();

    if (!$attendance->check_in) {
        return back()->with('error', '❌ Please check-in first.');
    }

    if ($attendance->check_out) {
        return back()->with('error', '❌ Already checked out.');
    }

    $checkIn = Carbon::parse($attendance->check_in);
    $checkOut = now();

    // Handle midnight checkout (after 12 AM)
    if ($checkOut->lt($checkIn)) {
        $checkOut->addDay();
    }

    // Calculate total working time
    $totalSeconds = $checkIn->diffInSeconds($checkOut);
    $totalMinutes = floor($totalSeconds / 60);

    // Subtract break time (1 hour = 60 minutes)
    $netMinutes = max(0, $totalMinutes - 60);
    $netHours = floor($netMinutes / 60);
    $netRemainingMinutes = $netMinutes % 60;
    $netWorkingHours = sprintf('%02d:%02d:00', $netHours, $netRemainingMinutes);

    // Total working hours (with break)
    $totalHours = floor($totalSeconds / 3600);
    $totalMinutesRemaining = floor(($totalSeconds % 3600) / 60);
    $totalWorkingHours = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutesRemaining, $totalSeconds % 60);

    // Determine final status
    $status = $attendance->status;
    $remarks = "Checked out at {$checkOut->format('h:i A')}. Net worked: {$netHours}h {$netRemainingMinutes}m";

    if ($netHours < 4) {
        $status = 'Half Day';
        $remarks = "Worked less than 4 hours ({$netHours}h {$netRemainingMinutes}m)";
    } elseif ($status == 'Late' && $netHours >= 9) {
        $remarks = "Late but completed full day. Net worked: {$netHours}h {$netRemainingMinutes}m";
    } elseif ($netHours >= 9) {
        $remarks = "Completed full day ({$netHours}h {$netRemainingMinutes}m)";
    }

    $attendance->update([
        'check_out' => $checkOut->format('H:i:s'),
        'working_hours' => $totalWorkingHours,
        'net_working_hours' => $netWorkingHours,
        'status' => $status,
        'remarks' => $remarks,
    ]);

    Log::info('Employee checked out', [
        'employee_id' => $employee->id,
        'check_in' => $attendance->check_in,
        'check_out' => $checkOut->format('H:i:s'),
        'net_working_hours' => $netWorkingHours,
        'status' => $status
    ]);

    $message = "✅ Check-out successful at {$checkOut->format('h:i A')}<br>
                📊 Total Worked: {$netHours}h {$netRemainingMinutes}m (after 1h break)<br>
                🎯 Status: {$status}";

    return back()->with('success', $message);
}

    /* ================= ADMIN / HR ================= */

    public function manage()
    {
        // Check if user is admin or hr
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get filter parameters
        $date = request('date', today()->format('Y-m-d'));
        $status = request('status');
        $employee_id = request('employee_id');

        $query = Attendance::with(['employee.user']);

        // Apply filters
        if ($date) {
            $query->whereDate('attendance_date', $date);
        }

        if ($status && $status != 'all') {
            $query->where('status', ucfirst(strtolower($status)));
        }

        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }

        $records = $query->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get employees for filter dropdown
        $employees = Employee::where('status', 'active')->get();

        // Get today's summary
        $todaySummary = [
            'total_employees' => Employee::where('status', 'active')->count(),
            'present' => Attendance::whereDate('attendance_date', today())
                ->where(function($q) {
                    $q->where('status', 'Present')
                      ->orWhere('status', 'present');
                })
                ->count(),
            'absent' => Attendance::whereDate('attendance_date', today())
                ->where(function($q) {
                    $q->where('status', 'Absent')
                      ->orWhere('status', 'absent');
                })
                ->count(),
            'late' => Attendance::whereDate('attendance_date', today())
                ->where(function($q) {
                    $q->where('status', 'Late')
                      ->orWhere('status', 'late');
                })
                ->count(),
            'half_day' => Attendance::whereDate('attendance_date', today())
                ->where('status', 'Half Day')
                ->count(),
        ];

        $todaySummary['not_marked'] = $todaySummary['total_employees'] -
            ($todaySummary['present'] + $todaySummary['absent'] +
             $todaySummary['late'] + $todaySummary['half_day']);

        return view('attendance.manage', compact(
            'records',
            'employees',
            'todaySummary',
            'date',
            'status',
            'employee_id'
        ));
    }

    /**
     * HR Attendance Marking Page
     */
    public function markAttendance()
    {
        // Check if user is admin or hr
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get all active employees
        $employees = Employee::where('status', 'active')->get();

        // Get today's attendance
        $todayAttendance = Attendance::whereDate('attendance_date', today())
            ->with('employee')
            ->get()
            ->keyBy('employee_id');

        // Get attendance status counts
        $attendanceCounts = [
            'present' => Attendance::whereDate('attendance_date', today())
                ->where(function($query) {
                    $query->where('status', 'present')
                          ->orWhere('status', 'Present');
                })
                ->count(),
            'absent' => Attendance::whereDate('attendance_date', today())
                ->where(function($query) {
                    $query->where('status', 'absent')
                          ->orWhere('status', 'Absent');
                })
                ->count(),
            'late' => Attendance::whereDate('attendance_date', today())
                ->where(function($query) {
                    $query->where('status', 'late')
                          ->orWhere('status', 'Late');
                })
                ->count(),
            'half_day' => Attendance::whereDate('attendance_date', today())
                ->where('status', 'Half Day')
                ->count(),
            'pending' => Employee::where('status', 'active')->count() -
                Attendance::whereDate('attendance_date', today())->count()
        ];

        return view('attendance.mark', compact(
            'employees',
            'todayAttendance',
            'attendanceCounts'
        ));
    }

    /**
     * Submit Bulk Attendance
     */
 /**
 * Submit Bulk Attendance with proper working hours calculation
 */
public function bulkAttendance(Request $request)
{
    if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'attendances' => 'required|array',
        'attendances.*.employee_id' => 'required|exists:employees,id',
        'attendances.*.status' => 'required|in:present,absent,late,leave',
        'attendances.*.check_in' => 'nullable|date_format:H:i',
        'attendances.*.remarks' => 'nullable|string|max:255',
        'attendance_date' => 'required|date'
    ]);

    DB::beginTransaction();
    try {
        $markedCount = 0;
        $attendanceDate = $validated['attendance_date'];

        foreach ($validated['attendances'] as $attendanceData) {
            $status = ucfirst($attendanceData['status']);
            $checkIn = $attendanceData['check_in'] ?? null;

            // Auto-calculate check-in time for present/late
            if (in_array($attendanceData['status'], ['present', 'late']) && !$checkIn) {
                $checkIn = now()->format('H:i');
            }

            // Determine status if check-in time provided
            if ($checkIn && $attendanceData['status'] !== 'leave') {
                $lateTime = Carbon::createFromTime(15, 15); // 3:15 PM
                $checkInTime = Carbon::createFromTimeString($checkIn);

                if ($checkInTime->gt($lateTime)) {
                    $status = 'Late';
                } else {
                    $status = 'Present';
                }
            }

            // For absent or leave, clear check-in/out times
            if (in_array($attendanceData['status'], ['absent', 'leave'])) {
                $checkIn = null;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'employee_id' => $attendanceData['employee_id'],
                    'attendance_date' => $attendanceDate
                ],
                [
                    'status' => $status,
                    'check_in' => $checkIn,
                    'remarks' => $attendanceData['remarks'] ?? null,
                    'marked_by' => Auth::id(),
                    'is_auto_marked' => false
                ]
            );

            // Calculate working hours if check-in exists
            if ($attendance->check_in && $attendance->check_out) {
                $attendance->working_hours = $attendance->calculateWorkingHours();
                $attendance->save();
            }

            $markedCount++;
        }

        DB::commit();

        Log::info('Bulk attendance marked', [
            'marked_by' => Auth::id(),
            'date' => $attendanceDate,
            'count' => $markedCount
        ]);

        return response()->json([
            'success' => true,
            'message' => "Attendance marked successfully for {$markedCount} employees",
            'count' => $markedCount
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Bulk attendance error: ' . $e->getMessage());

        return response()->json([
            'error' => 'Failed to mark attendance',
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Auto Mark Absent for employees who didn't check-in
     */
    public function autoMarkAbsent()
    {
        // Check if user is admin or hr
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = today();
        $cutoffTime = now()->setTime(17, 0, 0); // 5:00 PM cutoff

        // Only run auto-mark after cutoff time
        if (now()->lt($cutoffTime)) {
            return response()->json([
                'error' => 'Auto-mark can only run after ' . $cutoffTime->format('h:i A')
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Get all active employees
            $activeEmployees = Employee::where('status', 'active')->pluck('id');

            // Get employees who have attendance marked today
            $markedEmployees = Attendance::whereDate('attendance_date', $today)
                ->pluck('employee_id');

            // Employees without attendance
            $absentEmployees = $activeEmployees->diff($markedEmployees);

            $markedCount = 0;
            foreach ($absentEmployees as $employeeId) {
                Attendance::create([
                    'employee_id' => $employeeId,
                    'attendance_date' => $today,
                    'status' => 'Absent',
                    'remarks' => 'Auto-marked absent (no check-in)',
                    'marked_by' => Auth::id(),
                    'is_auto_marked' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $markedCount++;
            }

            DB::commit();

            Log::info('Auto-marked absent employees', [
                'date' => $today,
                'count' => $markedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Auto-marked ' . $markedCount . ' employees as absent',
                'count' => $markedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto-mark absent error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to auto-mark absent',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit Attendance
     */
    public function edit($id)
    {
        // Check if user is admin or hr
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $attendance = Attendance::with('employee')->findOrFail($id);
        $employees = Employee::where('status', 'active')->get();

        return view('attendance.edit', compact('attendance', 'employees'));
    }

    /**
     * Update Attendance
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin or hr
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:Present,Absent,Late,Half Day,Leave',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'working_hours' => 'nullable|date_format:H:i:s',
            'remarks' => 'nullable|string|max:500',
        ]);

        $attendance = Attendance::findOrFail($id);

        // Update attendance
        $attendance->update([
            'employee_id' => $validated['employee_id'],
            'status' => $validated['status'],
            'attendance_date' => $validated['attendance_date'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'working_hours' => $validated['working_hours'],
            'remarks' => $validated['remarks'],
            'marked_by' => Auth::id(),
            'updated_at' => now()
        ]);

        // Log the update
        Log::info('Attendance updated', [
            'attendance_id' => $id,
            'updated_by' => Auth::id(),
            'changes' => $validated
        ]);

        return redirect()->route('attendance.manage')
            ->with('success', '✅ Attendance updated successfully');
    }

    /**
     * Delete Attendance
     */
    public function destroy($id)
    {
        // Check if user is admin or hr
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $attendance = Attendance::findOrFail($id);

            // Log before deletion
            Log::info('Attendance deleted', [
                'attendance_id' => $id,
                'employee_id' => $attendance->employee_id,
                'date' => $attendance->attendance_date,
                'deleted_by' => Auth::id()
            ]);

            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance deletion error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to delete attendance',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attendance Report
     */
    public function report()
    {
        // Check if user is admin or hr
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $month = request('month', now()->format('Y-m'));
        $employee_id = request('employee_id');

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $query = Attendance::with('employee')
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        // Get summary
        $summary = [
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'present' => $attendances->where('status', 'Present')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'leave' => $attendances->where('status', 'Leave')->count(),
        ];

        $employees = Employee::where('status', 'active')->get();

        return view('attendance.report', compact(
            'attendances',
            'summary',
            'employees',
            'month',
            'employee_id'
        ));
    }

    /**
     * Attendance Summary for Dashboard
     */
    public function getTodayAttendanceSummary()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $today = today();

        $summary = [
            'total_employees' => Employee::where('status', 'active')->count(),
            'present' => Attendance::whereDate('attendance_date', $today)
                ->where(function($q) {
                    $q->where('status', 'Present')
                      ->orWhere('status', 'present');
                })
                ->count(),
            'absent' => Attendance::whereDate('attendance_date', $today)
                ->where(function($q) {
                    $q->where('status', 'Absent')
                      ->orWhere('status', 'absent');
                })
                ->count(),
            'late' => Attendance::whereDate('attendance_date', $today)
                ->where(function($q) {
                    $q->where('status', 'Late')
                      ->orWhere('status', 'late');
                })
                ->count(),
            'half_day' => Attendance::whereDate('attendance_date', $today)
                ->where('status', 'Half Day')
                ->count(),
        ];

        $summary['not_marked'] = $summary['total_employees'] -
            ($summary['present'] + $summary['absent'] +
             $summary['late'] + $summary['half_day']);

        // For dashboard chart
        $chartData = [
            'labels' => ['Present', 'Absent', 'Late', 'Half Day', 'Not Marked'],
            'data' => [
                $summary['present'],
                $summary['absent'],
                $summary['late'],
                $summary['half_day'],
                $summary['not_marked']
            ],
            'colors' => ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#6b7280']
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'chart_data' => $chartData,
            'last_updated' => now()->format('h:i A')
        ]);
    }
}
