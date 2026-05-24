<?php

namespace App\Http\Controllers;

class OwnerController extends Controller
{
    /**
     * Display the employees management page.
     */
    public function employees()
    {
        return view('owner.employees');
    }
}
