<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\EmployeeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /* ================= SEND EMAIL ================= */
 public function sendEmail(Request $request, $id = null)
{
    // Debug - Log incoming request
    Log::info('=== REQUEST DEBUG ===');
    Log::info('Request URL: ' . $request->fullUrl());
    Log::info('Request Method: ' . $request->method());
    Log::info('Employee ID from route: ' . $id);
    Log::info('All request data: ', $request->all());

    // Validate
    $request->validate([
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
    ]);

    try {
        // Get employee ID from route or request
        $employeeId = $id ?? $request->route('employee') ?? $request->input('employee_id');

        Log::info('Looking for employee with ID: ' . $employeeId);

        // Manually find employee
        if (!$employeeId) {
            throw new \Exception('Employee ID not provided');
        }

        $employee = Employee::find($employeeId);

        if (!$employee) {
            throw new \Exception('Employee not found with ID: ' . $employeeId);
        }

        Log::info('Employee found: ', ['id' => $employee->id, 'name' => $employee->name]);

        $sender = Auth::user();

        // Get email from user relationship
        $user = $employee->user;
        $employeeEmail = $user ? $user->email : $employee->email;

        if (!$employeeEmail) {
            throw new \Exception('Employee has no email address');
        }

        $employeeName = $user ? $user->name : $employee->name;

        Log::info('Sending email to: ' . $employeeEmail);

        // Send email
        Mail::to($employeeEmail, $employeeName)->send(new EmployeeEmail(
            $request->subject,
            $request->body,
            $sender,
            $employee
        ));

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully to ' . $employeeEmail
        ]);

    } catch (\Exception $e) {
        Log::error('Email sending failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to send email: ' . $e->getMessage()
        ], 500);
    }
}

    /* ================= LIST ================= */
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $employees = Employee::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search', 'perPage'));
    }

    /* ================= SHOW ================= */
    public function show($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    /* ================= CREATE ================= */
    public function create()
    {
        return view('employees.create');
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'staff',
            'status'   => 1,
        ]);

        $code = 'EMP' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT);

        Employee::create([
            'user_id'       => $user->id,
            'employee_code' => $code,
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'department'    => $request->department,
            'joining_date'  => $request->joining_date,
            'status'        => 1,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully');
    }

    /* ================= EDIT ================= */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $user = User::findOrFail($employee->user_id);

        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        $userData = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        $employee->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'department'   => $request->department,
            'joining_date' => $request->joining_date,
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully');
    }

    /* ================= DELETE ================= */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        User::where('id', $employee->user_id)->delete();
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted');
    }
}
