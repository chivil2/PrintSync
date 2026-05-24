<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    /**
     * Display the employees management page.
     */
    public function employees()
    {
        return view('admin.employees');
    }
}
