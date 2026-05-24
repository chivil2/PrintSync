<?php

namespace App\Http\Controllers;

use App\Models\User;

class OwnerController extends Controller
{
    /**
     * Display the employees management page.
     */
    public function employees()
    {
        $employees = User::role('employee')->get();

        return view('owner.employees', [
            'employees' => $employees,
        ]);
    }
}
