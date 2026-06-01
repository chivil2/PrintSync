<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class OwnerController extends Controller
{
    /**
     * Display the owner dashboard.
     */
    public function dashboard(Request $request)
    {
        $data['user'] = auth()->user();
        $data['recentJobs'] = ServiceJob::with(['customer', 'service'])->latest()->take(5)->get();
        $data['employees'] = User::role('employee')->where('employee_status', 'active')->latest()->take(5)->get();
        $data['jobs'] = ServiceJob::with(['customer', 'service', 'employee'])->latest()->take(10)->get();
        $data['allJobs'] = ServiceJob::with(['customer', 'service', 'employee'])->latest()->get();

        // Stat card data - compute from completed service jobs
        $completedJobIds = ServiceJob::where('status', 'completed')->pluck('id');
        $data['totalRevenue'] = Quote::whereIn('service_job_id', $completedJobIds)->sum('total') ?? 0;
        $data['totalOrders'] = Quote::whereIn('service_job_id', $completedJobIds)->count();
        $data['activeCustomers'] = Quote::whereIn('service_job_id', $completedJobIds)->distinct('customer_id')->count('customer_id');
        $data['completedJobs'] = ServiceJob::where('status', 'completed')->count();

        return view('owner.dashboard', $data);
    }

    /**
     * Display the employees management page.
     */
    public function employees(Request $request)
    {
        $query = User::role('employee');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('employee_status', $request->status);
        }

        $employees = $query->latest()->paginate(12);

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
            'profile_photo_path' => ['nullable', 'image', 'max:2048'],
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo_path')) {
            $profilePhotoPath = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

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
            'profile_photo_path' => $profilePhotoPath,
        ]);

        $user->assignRole('employee');

        return redirect()->route('owner.employees')
            ->with('success', 'Employee added successfully.');
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function editEmployee(User $employee)
    {
        return view('owner.employees-edit', compact('employee'));
    }

    /**
     * Update the specified employee.
     */
    public function updateEmployee(Request $request, User $employee)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$employee->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'hire_date' => ['nullable', 'date'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'employee_status' => ['nullable', 'string', 'in:active,inactive'],
            'profile_photo_path' => ['nullable', 'image', 'max:2048'],
        ]);

        $profilePhotoPath = $employee->profile_photo_path;
        if ($request->hasFile('profile_photo_path')) {
            if ($employee->profile_photo_path) {
                Storage::disk('public')->delete($employee->profile_photo_path);
            }
            $profilePhotoPath = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

        $employee->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'employee_status' => $validated['employee_status'] ?? 'active',
            'profile_photo_path' => $profilePhotoPath,
        ]);

        return redirect()->route('owner.employees')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Toggle the status of the specified employee.
     */
    public function toggleEmployeeStatus(User $employee)
    {
        $newStatus = $employee->employee_status === 'active' ? 'inactive' : 'active';
        $employee->update(['employee_status' => $newStatus]);

        return redirect()->route('owner.employees')
            ->with('success', "Employee status changed to {$newStatus}.");
    }

    /**
     * Remove the specified employee.
     */
    public function destroyEmployee(User $employee)
    {
        $employee->delete();

        return redirect()->route('owner.employees')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Display the quotes management page.
     */
    public function quotes()
    {
        $quotes = Quote::with(['customer', 'serviceJob', 'lineItems'])
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
            ->withCount(['serviceJobs as assigned_jobs_count' => function ($query) {
                $query->whereIn('status', ['pending', 'in_progress']);
            }])
            ->get();

        // Get job statistics
        $jobsCountByStatus = ServiceJob::select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalJobs = array_sum($jobsCountByStatus);
        $completedJobs = $jobsCountByStatus['completed'] ?? 0;
        $completionPercent = $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100) : 0;

        // Pending quote approval (draft or sent) - these are waiting for customer
        $pendingQuoteJobs = ServiceJob::with(['customer', 'quote'])
            ->whereHas('quote', function ($q) {
                $q->whereIn('status', ['draft', 'sent']);
            })
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        // Ready to assign (quote accepted) - these can be assigned to employees
        $readyToAssignJobs = ServiceJob::with(['customer', 'employee', 'quote'])
            ->whereHas('quote', function ($q) {
                $q->where('status', 'accepted');
            })
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        $unassignedJobsCount = $readyToAssignJobs->whereNull('employee_id')->count();

        // Notification badge count - accepted quotes needing assignment
        $acceptedQuotesNeedingAssignment = Quote::where('status', 'accepted')
            ->whereHas('serviceJob', function ($q) {
                $q->whereNull('employee_id');
            })
            ->count();

        return view('owner.jobs', [
            'jobs' => $jobs,
            'employees' => $employees,
            'jobsCountByStatus' => $jobsCountByStatus,
            'completionPercent' => $completionPercent,
            'pendingQuoteJobs' => $pendingQuoteJobs,
            'readyToAssignJobs' => $readyToAssignJobs,
            'unassignedJobs' => $readyToAssignJobs,
            'unassignedJobsCount' => $unassignedJobsCount,
            'acceptedQuotesNeedingAssignment' => $acceptedQuotesNeedingAssignment,
        ]);
    }

    /**
     * Display the job detail page.
     */
    public function showJob(ServiceJob $job)
    {
        $job->load(['customer', 'employee', 'service', 'quote', 'quote.lineItems']);

        $employees = User::role('employee')
            ->where('employee_status', 'active')
            ->get();

        return view('owner.job-detail', compact('job', 'employees'));
    }

    /**
     * Assign an employee to a job.
     */
    public function assignEmployee(Request $request, ServiceJob $job)
    {
        $validated = $request->validate([
            'employee_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($validated['employee_id']) {
            $job->update([
                'employee_id' => $validated['employee_id'],
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        } else {
            $job->update([
                'employee_id' => null,
                'status' => 'pending',
                'started_at' => null,
            ]);
        }

        $employee = $validated['employee_id'] ? User::find($validated['employee_id']) : null;

        if ($employee) {
            return back()->with('success', "{$employee->first_name} {$employee->last_name} assigned to \"{$job->name}\"");
        }

        return back()->with('success', "Employee unassigned from \"{$job->name}\"");
    }

    /**
     * Assign an employee to a job via API (JSON response).
     */
    public function assignEmployeeApi(Request $request, ServiceJob $job)
    {
        // Check if quote is accepted before allowing assignment
        if ($job->quote && $job->quote->status !== 'accepted') {
            $message = match ($job->quote->status) {
                'draft' => 'Quote is still in draft. Please review and send it to the customer first.',
                'sent' => 'Quote has been sent to customer but not yet accepted. Waiting for customer approval.',
                'rejected' => 'Quote was rejected by customer. Please create a new quote.',
                default => 'Quote must be accepted by customer before assigning an employee.',
            };

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->with('error', $message);
        }

        $validated = $request->validate([
            'employee_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($validated['employee_id']) {
            $job->update([
                'employee_id' => $validated['employee_id'],
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        } else {
            $job->update([
                'employee_id' => null,
                'status' => 'pending',
                'started_at' => null,
            ]);
        }

        $employee = $validated['employee_id'] ? User::find($validated['employee_id']) : null;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $employee
                    ? "{$employee->first_name} {$employee->last_name} assigned to \"{$job->name}\""
                    : "Employee unassigned from \"{$job->name}\"",
                'job' => $job->load('employee'),
            ]);
        }

        return redirect()->route('owner.jobs.show', $job)->with(
            'success',
            $employee
                ? "{$employee->first_name} {$employee->last_name} assigned to \"{$job->name}\""
                : "Employee unassigned from \"{$job->name}\""
        );
    }

    /**
     * Remove the specified job from the database.
     */
    public function destroyJob(ServiceJob $job)
    {
        $job->delete();

        return redirect()->route('owner.jobs')
            ->with('success', 'Job deleted successfully.');
    }

    public function earningsByPeriod(Request $request): JsonResponse
    {
        $dashboardService = new DashboardDataService;

        return response()->json(
            $dashboardService->getEarningsByPeriod($request->get('period', 'month'))
        );
    }
}
