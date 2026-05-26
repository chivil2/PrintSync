<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
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

        return view('employee.dashboard', [
            'totalJobs' => $totalJobs,
            'completedJobs' => $completedJobs,
            'inProgressJobs' => $inProgressJobs,
            'pendingJobs' => $pendingJobs,
            'recentJobs' => $recentJobs,
        ]);
    }

    public function quotes()
    {
        return view('employee.quotes');
    }

    public function jobs()
    {
        $jobs = ServiceJob::where('employee_id', auth()->id())
            ->with(['customer', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.jobs', [
            'jobs' => $jobs,
        ]);
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

        return redirect()->route('employee.jobs')->with('success', 'Job status updated successfully.');
    }
}
