<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::where('is_admin', 0)->get();

        return view('admin.staff.list', compact('staff'));
    }
}
