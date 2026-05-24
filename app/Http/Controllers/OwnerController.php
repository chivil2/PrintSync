<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

    /**
     * Show the form for creating a new employee.
     */
    public function createEmployee()
    {
        return view('owner.employees-create');
    }

    /**
     * Store a newly created employee.
     */
    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', Password::default()],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'hire_date' => ['nullable', 'date'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'employee_status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'employee_id' => $validated['employee_id'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'employee_status' => $validated['employee_status'] ?? 'active',
        ]);

        $user->assignRole('employee');

        return redirect()->route('owner.employees')
            ->with('success', 'Employee added successfully.');
    }

    /**
     * Display the quotes management page.
     */
    public function quotes()
    {
        $quotes = Quote::with(['customer', 'employee', 'lineItems'])
            ->latest()
            ->paginate(20);

        return view('owner.quotes', [
            'quotes' => $quotes,
        ]);
    }

    /**
     * Display the jobs management page.
     */
    public function jobs()
    {
        $jobs = ServiceJob::with(['customer', 'employee', 'service'])
            ->latest()
            ->paginate(20);

        $employees = User::role('employee')
            ->where('employee_status', 'active')
            ->get();

        return view('owner.jobs', [
            'jobs' => $jobs,
            'employees' => $employees,
        ]);
    }

    /**
     * Assign an employee to a job.
     */
    public function assignEmployee(Request $request, ServiceJob $job)
    {
        $validated = $request->validate([
            'employee_id' => ['nullable', 'exists:users,id'],
        ]);

        $job->update([
            'employee_id' => $validated['employee_id'],
        ]);

        $employee = $validated['employee_id'] ? User::find($validated['employee_id']) : null;

        if ($employee) {
            return back()->with('success', "{$employee->first_name} {$employee->last_name} assigned to \"{$job->name}\"");
        }

        return back()->with('success', "Employee unassigned from \"{$job->name}\"");
    }
}
