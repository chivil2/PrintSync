<?php

namespace App\Http\Controllers;

use App\Concerns\ProfileValidationRules;
use App\Models\PrintingService;
use App\Models\ServiceJob;
use App\Models\TechnicalService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    use ProfileValidationRules;

    public function dashboard()
    {
        $orders = ServiceJob::where('customer_id', auth()->id())
            ->with(['service', 'employee'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $totalSpent = $orders->where('status', 'completed')->sum('price');

        $recentOrders = $orders->take(5);

        return view('customer.dashboard', [
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'totalSpent' => $totalSpent,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function store()
    {
        $printingServices = PrintingService::where('is_active', true)->get();
        $technicalServices = TechnicalService::where('is_active', true)->get();

        return view('customer.store', [
            'printingServices' => $printingServices,
            'technicalServices' => $technicalServices,
        ]);
    }

    public function orders()
    {
        $orders = ServiceJob::where('customer_id', auth()->id())
            ->with(['service', 'employee'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.orders', [
            'orders' => $orders,
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        $hasUnverifiedEmail = $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();

        return view('customer.profile', [
            'user' => $user,
            'message' => "lol",
            'hasUnverifiedEmail' => $hasUnverifiedEmail,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('customer.profile')->with('success', 'LOL. Profile updated successfully.');
    }

    public function resendVerification(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('customer.store');
        }

        $user->sendEmailVerificationNotification();

        return redirect()->route('customer.profile')->with('success', 'A new verification link has been sent to your email address.');
    }
}
