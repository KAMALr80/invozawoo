<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStaffController extends Controller
{
    // Pending staff list
    public function index()
    {
        $staff = User::where('role', 'staff')
                     ->where('status', 'pending')
                     ->get();

        return view('admin.staff', compact('staff'));
    }

    // Approve staff
    public function approve($id)
    {
        User::where('id', $id)->update([
            'status' => 'approved'
        ]);

        return back()->with('success', 'Staff approved successfully');
    }
}
