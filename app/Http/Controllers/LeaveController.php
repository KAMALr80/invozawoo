<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveHoliday;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* =========================
       DASHBOARD
    ==========================*/
    public function dashboard()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('leaves.my');
        }

        // Statistics
        $statistics = [
            'total_pending' => Leave::where('status', 'Pending')->count(),
            'total_approved' => Leave::where('status', 'Approved')->count(),
            'total_rejected' => Leave::where('status', 'Rejected')->count(),
            'total_cancelled' => Leave::where('status', 'Cancelled')->count(),
            'total_leaves' => Leave::count(),
            'employees_on_leave_today' => Leave::where('status', 'Approved')
                ->whereDate('from_date', '<=', now())
                ->whereDate('to_date', '>=', now())
                ->count(),
            'leaves_this_month' => Leave::whereYear('from_date', now()->year)
                ->whereMonth('from_date', now()->month)
                ->count(),
            'upcoming_leaves' => Leave::where('status', 'Approved')
                ->whereDate('from_date', '>', now())
                ->whereDate('from_date', '<=', now()->addDays(7))
                ->count(),
        ];

        // Monthly data for chart
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = Leave::whereYear('from_date', now()->year)
                ->whereMonth('from_date', $i)
                ->count();
        }

        // Leave by type
        $leaveByType = [
            'annual' => Leave::where('leave_type', 'annual')->count(),
            'sick' => Leave::where('leave_type', 'sick')->count(),
            'casual' => Leave::where('leave_type', 'casual')->count(),
            'unpaid' => Leave::where('leave_type', 'unpaid')->count(),
        ];

        // Recent activities
        $recentLeaves = Leave::with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top leave takers
        $topLeaveTakers = Employee::withCount(['leaves' => function($q) {
                $q->whereYear('from_date', now()->year)
                  ->where('status', 'Approved');
            }])
            ->orderBy('leaves_count', 'desc')
            ->limit(5)
            ->get();

        return view('leaves.dashboard', compact(
            'statistics',
            'monthlyData',
            'leaveByType',
            'recentLeaves',
            'topLeaveTakers'
        ));
    }

    /* =========================
       STAFF – CREATE LEAVE FORM
    ==========================*/
    public function create()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->route('home')->with('error', 'Employee record not found');
        }

        // Get leave balances
        $leaveBalances = $this->getEmployeeLeaveBalances($employee->id);

        // Get upcoming holidays
        $upcomingHolidays = LeaveHoliday::upcoming(5)->get();

        return view('leaves.create', compact('employee', 'leaveBalances', 'upcomingHolidays'));
    }

    /* =========================
       STAFF – STORE LEAVE
    ==========================*/
    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return back()->with('error', 'Employee record not found');
        }

        $request->validate([
            'leave_type' => 'required|in:annual,sick,casual,unpaid,maternity,paternity,bereavement,study,half_day,short_leave',
            'duration_type' => 'required|in:full_day,half_day,short_leave',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required_if:duration_type,full_day|date|after_or_equal:from_date',
            'session' => 'required_if:duration_type,half_day|in:first_half,second_half',
            'start_time' => 'required_if:duration_type,short_leave|date_format:H:i',
            'end_time' => 'required_if:duration_type,short_leave|date_format:H:i|after:start_time',
            'reason' => 'required|string|min:10|max:2000',
            'contact_number' => 'nullable|string|max:20',
            'handover_notes' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ]);

        // Calculate total days
        $totalDays = $this->calculateLeaveDays($request);

        // Check if enough balance (for paid leaves)
        if (!in_array($request->leave_type, ['unpaid', 'half_day', 'short_leave'])) {
            $balance = LeaveBalance::where('employee_id', $employee->id)
                ->where('year', date('Y'))
                ->where('leave_type', $request->leave_type)
                ->first();

            if (!$balance || $balance->total_available < $totalDays) {
                return back()->with('error', 'Insufficient leave balance')->withInput();
            }
        }

        // Check overlapping leaves
        if ($this->hasOverlappingLeave($employee->id, $request)) {
            return back()->with('error', 'You already have a leave request for these dates')->withInput();
        }

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('leave-documents', 'public');
        }

        // Generate leave number
        $leaveNumber = 'LVE-' . date('Y') . str_pad(Leave::count() + 1, 6, '0', STR_PAD_LEFT);

        // Create leave
        $leave = Leave::create([
            'employee_id' => $employee->id,
            'leave_number' => $leaveNumber,
            'leave_type' => $request->leave_type,
            'duration_type' => $request->duration_type,
            'from_date' => $request->from_date,
            'to_date' => $request->duration_type == 'full_day' ? $request->to_date : $request->from_date,
            'total_days' => $totalDays,
            'session' => $request->session,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'contact_number' => $request->contact_number,
            'handover_notes' => $request->handover_notes,
            'document_path' => $documentPath,
            'status' => 'Pending',
            'applied_on' => now(),
        ]);

        // Update pending balance
        if (!in_array($request->leave_type, ['unpaid', 'half_day', 'short_leave'])) {
            $balance = LeaveBalance::where('employee_id', $employee->id)
                ->where('year', date('Y'))
                ->where('leave_type', $request->leave_type)
                ->first();

            if ($balance) {
                $balance->pending += $totalDays;
                $balance->calculateTotals();
            }
        }

        return redirect()->route('leaves.my')->with('success', '✅ Leave applied successfully');
    }

    /* =========================
       STAFF – MY LEAVES
    ==========================*/
    public function myLeaves(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return back()->with('error', 'Employee record not found');
        }

        $query = Leave::where('employee_id', $employee->id);

        // Filters
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->whereYear('from_date', $request->year);
        }

        if ($request->filled('leave_type') && $request->leave_type != 'all') {
            $query->where('leave_type', $request->leave_type);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Available years for filter
        $years = Leave::where('employee_id', $employee->id)
            ->selectRaw('YEAR(from_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('leaves.my', compact('leaves', 'years'));
    }

    /* =========================
       STAFF – VIEW LEAVE DETAILS
    ==========================*/
  public function show($id)
{
    $user = Auth::user();
    $employee = Employee::where('user_id', $user->id)->first();

    if (!$employee) {
        return redirect()->route('leaves.my')->with('error', 'Employee record not found.');
    }

    $leave = Leave::with('employee')
        ->where('id', $id)
        ->where('employee_id', $employee->id)  // This restricts to only the current user's leaves
        ->first();

    // ...
}

    /* =========================
       STAFF – CANCEL LEAVE
    ==========================*/
    public function cancel($id)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        $leave = Leave::where('id', $id)
            ->where('employee_id', $employee->id)
            ->where('status', 'Pending')
            ->firstOrFail();

        // Update pending balance
        if (!in_array($leave->leave_type, ['unpaid', 'half_day', 'short_leave'])) {
            $balance = LeaveBalance::where('employee_id', $employee->id)
                ->where('year', date('Y'))
                ->where('leave_type', $leave->leave_type)
                ->first();

            if ($balance) {
                $balance->pending -= $leave->total_days;
                $balance->calculateTotals();
            }
        }

        $leave->update([
            'status' => 'Cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id()
        ]);

        return redirect()->route('leaves.my')->with('success', '✅ Leave cancelled successfully');
    }

    /* =========================
       ADMIN – MANAGE LEAVES
    ==========================*/


    public function printLeave($id)
{
    // Check if user is admin
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized access.');
    }

    $leave = Leave::with(['employee', 'approver', 'rejector', 'canceller'])
        ->findOrFail($id);

    // Return a print-friendly view
    return view('leaves.print', compact('leave'));
}
    public function manage(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $query = Leave::with('employee');

        // Apply filters
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type') && $request->leave_type != 'all') {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('from_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('to_date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('leave_number', 'LIKE', "%{$search}%")
                  ->orWhere('reason', 'LIKE', "%{$search}%")
                  ->orWhereHas('employee', function($emp) use ($search) {
                      $emp->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('employee_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 15);
        $leaves = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        // Statistics
        $statistics = [
            'pending' => Leave::where('status', 'Pending')->count(),
            'approved' => Leave::where('status', 'Approved')->count(),
            'rejected' => Leave::where('status', 'Rejected')->count(),
            'cancelled' => Leave::where('status', 'Cancelled')->count(),
        ];

        return view('leaves.manage', compact('leaves', 'statistics'));
    }

    /* =========================
       ADMIN – ADMIN SHOW LEAVE
    ==========================*/
    public function adminShow($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $leave = Leave::with(['employee', 'approver', 'rejector', 'canceller'])->findOrFail($id);

        // Get employee's leave balance
        $leaveBalance = $this->getEmployeeLeaveBalances($leave->employee_id);

        // Get employee's leave history
        $leaveHistory = Leave::where('employee_id', $leave->employee_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('leaves.admin-show', compact('leave', 'leaveBalance', 'leaveHistory'));
    }

    /* =========================
       ADMIN – APPROVE LEAVE
    ==========================*/
    public function approve(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'Pending') {
            return back()->with('error', '❌ This leave request cannot be approved');
        }

        DB::transaction(function() use ($leave, $request) {
            // Update leave
            $leave->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_remarks' => $request->remarks
            ]);

            // Update balance
            if (!in_array($leave->leave_type, ['unpaid', 'half_day', 'short_leave'])) {
                $balance = LeaveBalance::where('employee_id', $leave->employee_id)
                    ->where('year', date('Y'))
                    ->where('leave_type', $leave->leave_type)
                    ->first();

                if ($balance) {
                    $balance->pending -= $leave->total_days;
                    $balance->used += $leave->total_days;
                    $balance->calculateTotals();
                }
            }
        });

        return back()->with('success', '✅ Leave approved successfully');
    }

    /* =========================
       ADMIN – REJECT LEAVE
    ==========================*/
    public function reject(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500'
        ]);

        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'Pending') {
            return back()->with('error', '❌ This leave request cannot be rejected');
        }

        DB::transaction(function() use ($leave, $request) {
            // Update leave
            $leave->update([
                'status' => 'Rejected',
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason
            ]);

            // Remove from pending balance
            if (!in_array($leave->leave_type, ['unpaid', 'half_day', 'short_leave'])) {
                $balance = LeaveBalance::where('employee_id', $leave->employee_id)
                    ->where('year', date('Y'))
                    ->where('leave_type', $leave->leave_type)
                    ->first();

                if ($balance) {
                    $balance->pending -= $leave->total_days;
                    $balance->calculateTotals();
                }
            }
        });

        return back()->with('success', '❌ Leave rejected successfully');
    }

    /* =========================
       HELPER FUNCTIONS
    ==========================*/
    private function getEmployeeLeaveBalances($employeeId)
    {
        $balances = LeaveBalance::where('employee_id', $employeeId)
            ->where('year', date('Y'))
            ->where('is_active', true)
            ->get();

        $result = [];
        foreach ($balances as $balance) {
            $result[$balance->leave_type] = [
                'entitled' => $balance->entitled,
                'used' => $balance->used,
                'pending' => $balance->pending,
                'available' => $balance->total_available - $balance->used - $balance->pending,
                'carry_forward' => $balance->carry_forward
            ];
        }

        // Add unpaid leave (always available)
        $result['unpaid'] = [
            'entitled' => 0,
            'used' => 0,
            'pending' => 0,
            'available' => 999,
            'carry_forward' => 0
        ];

        return $result;
    }

    private function calculateLeaveDays(Request $request)
    {
        if ($request->duration_type == 'full_day') {
            $from = Carbon::parse($request->from_date);
            $to = Carbon::parse($request->to_date);
            return $from->diffInDays($to) + 1;
        } elseif ($request->duration_type == 'half_day') {
            return 0.5;
        } else { // short_leave
            $start = Carbon::parse($request->start_time);
            $end = Carbon::parse($request->end_time);
            $hours = $end->diffInHours($start);
            return round($hours / 8, 2); // Assuming 8-hour work day
        }
    }

    private function hasOverlappingLeave($employeeId, Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->duration_type == 'full_day' ? $request->to_date : $request->from_date;

        return Leave::where('employee_id', $employeeId)
            ->whereIn('status', ['Pending', 'Approved'])
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('from_date', [$fromDate, $toDate])
                  ->orWhereBetween('to_date', [$fromDate, $toDate])
                  ->orWhere(function ($q2) use ($fromDate, $toDate) {
                      $q2->where('from_date', '<=', $fromDate)
                         ->where('to_date', '>=', $toDate);
                  });
            })
            ->exists();
    }

    /* =========================
   STAFF – APPLY LEAVE (SIMPLE FORM)
   Used by my.blade.php
==========================*/
public function apply(Request $request)
{
    $user = Auth::user();
    $employee = Employee::where('user_id', $user->id)->first();

    if (!$employee) {
        return back()->with('error', 'Employee record not found');
    }

    $request->validate([
        'from_date' => 'required|date|after_or_equal:today',
        'to_date'   => 'required|date|after_or_equal:from_date',
        'type'      => 'required|in:Paid,Unpaid,Sick,Half Day',
        'reason'    => 'nullable|string|max:500',
    ]);

    // Half Day validation
    if ($request->type === 'Half Day' && $request->from_date !== $request->to_date) {
        return back()->with('error', '❌ Half Day leave must be for a single date only')
                     ->withInput();
    }

    // Calculate days
    $from = Carbon::parse($request->from_date);
    $to = Carbon::parse($request->to_date);
    $days = $from->diffInDays($to) + 1;

    // For Half Day, set days to 0.5
    if ($request->type === 'Half Day') {
        $days = 0.5;
    }

    // Check overlapping leaves
    $overlap = Leave::where('employee_id', $employee->id)
        ->whereIn('status', ['Pending', 'Approved'])
        ->where(function ($q) use ($request) {
            $q->whereBetween('from_date', [$request->from_date, $request->to_date])
              ->orWhereBetween('to_date', [$request->from_date, $request->to_date])
              ->orWhere(function ($q2) use ($request) {
                  $q2->where('from_date', '<=', $request->from_date)
                     ->where('to_date', '>=', $request->to_date);
              });
        })
        ->exists();

    if ($overlap) {
        return back()->with('error', '❌ You already have a leave request for these dates')
                     ->withInput();
    }

    // Map old type to new leave_type
    $leaveType = match($request->type) {
        'Paid' => 'annual',
        'Unpaid' => 'unpaid',
        'Sick' => 'sick',
        'Half Day' => 'half_day',
        default => 'annual'
    };

    // Map to duration_type
    $durationType = $request->type === 'Half Day' ? 'half_day' : 'full_day';

    // Generate leave number
    $leaveNumber = 'LVE-' . date('Y') . str_pad(Leave::count() + 1, 6, '0', STR_PAD_LEFT);

    Leave::create([
        'employee_id' => $employee->id,
        'leave_number' => $leaveNumber,
        'leave_type' => $leaveType,
        'duration_type' => $durationType,
        'from_date' => $request->from_date,
        'to_date' => $request->to_date,
        'total_days' => $days,
        'session' => null, // Can be enhanced later
        'reason' => $request->reason,
        'status' => 'Pending',
        'applied_on' => now(),
    ]);

    return redirect()->route('leaves.my')->with('success', '✅ Leave applied successfully');
}
}
