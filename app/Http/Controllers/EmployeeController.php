<?php

namespace App\Http\Controllers;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        return view('employee.dashboard');
    }

    public function quotes()
    {
        return view('employee.quotes');
    }

    public function jobs()
    {
        return view('employee.jobs');
    }
}
