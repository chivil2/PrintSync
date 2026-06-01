<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\ServiceJob;
use App\Models\User;
use App\Notifications\JobCompletedNotification;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $assignedJobs = ServiceJob::where('employee_id', auth()->id())
            ->with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalJobs = $assignedJobs->count();
        $completedJobs = $assignedJobs->where('status', 'completed')->count();
        $inProgressJobs = $assignedJobs->where('status', 'in_progress')->count();
        $pendingJobs = $assignedJobs->where('status', 'pending')->count();

        $recentJobs = $assignedJobs->take(5);

        // Get jobs with deadlines for the current month
        $jobsWithDeadlines = ServiceJob::where('employee_id', auth()->id())
            ->whereNotNull('deadline')
            ->whereYear('deadline', now()->year)
            ->whereMonth('deadline', now()->month)
            ->with(['customer', 'service'])
            ->get()
            ->groupBy(function ($job) {
                return $job->deadline->format('Y-m-d');
            });

        // Get other active employees for team view
        $otherEmployees = User::where('id', '!=', auth()->id())
            ->whereHas('roles', function ($query) {
                $query->where('name', 'employee');
            })
            ->where('is_active', true)
            ->take(2)
            ->get();

        // Get jobs for right panel
        $jobs = ServiceJob::where('employee_id', auth()->id())
            ->with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('employee.dashboard', [
            'totalJobs' => $totalJobs,
            'completedJobs' => $completedJobs,
            'inProgressJobs' => $inProgressJobs,
            'pendingJobs' => $pendingJobs,
            'recentJobs' => $recentJobs,
            'jobsWithDeadlines' => $jobsWithDeadlines,
            'otherEmployees' => $otherEmployees,
            'jobs' => $jobs,
        ]);
    }

    public function quotes()
    {
        $quotes = Quote::whereHas('serviceJob', function ($query) {
            $query->where('employee_id', auth()->id());
        })
            ->with(['serviceJob', 'serviceJob.customer', 'lineItems'])
            ->orderBy('created_at', 'desc')
            ->distinct()
            ->get();

        return view('employee.quotes', [
            'quotes' => $quotes,
        ]);
    }

    public function jobs()
    {
        $jobs = ServiceJob::where('employee_id', auth()->id())
            ->with(['customer', 'service', 'quote'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.jobs', [
            'jobs' => $jobs,
        ]);
    }

    public function showJob(ServiceJob $job)
    {
        if ($job->employee_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $job->load(['customer', 'service', 'quote', 'quote.lineItems']);

        return view('employee.job-detail', compact('job'));
    }

    public function updateJobStatus(Request $request, ServiceJob $job)
    {
        if ($job->employee_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $job->update([
            'status' => $validated['status'],
            'started_at' => $validated['status'] === 'in_progress' ? now() : $job->started_at,
            'completed_at' => $validated['status'] === 'completed' ? now() : $job->completed_at,
        ]);

        if ($validated['status'] === 'completed') {
            User::role('owner')->first()?->notify(new JobCompletedNotification($job, 'owner'));
            $job->customer->notify(new JobCompletedNotification($job, 'customer'));

            if ($job->request_invoice) {
                $invoiceService = new InvoiceService;
                $invoiceService->generateInvoice($job);
            }
        }

        return redirect()->route('employee.jobs')->with('success', 'Job status updated successfully.');
    }

    public function getJobsByMonth(Request $request)
    {
        $year = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $jobs = ServiceJob::where('employee_id', auth()->id())
            ->whereNotNull('deadline')
            ->whereYear('deadline', $year)
            ->whereMonth('deadline', $month)
            ->with(['customer', 'service'])
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'name' => $job->name,
                    'priority' => $job->priority,
                    'status' => $job->status,
                    'customer_name' => $job->customer->name ?? 'Unknown',
                    'deadline' => $job->deadline->format('Y-m-d'),
                    'deadline_formatted' => $job->deadline->format('M d, Y'),
                ];
            })
            ->groupBy('deadline');

        return response()->json([
            'jobs_by_date' => $jobs,
        ]);
    }
}
