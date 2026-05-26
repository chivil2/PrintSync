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

    public function store(Request $request)
    {
        $search = $request->get('search');

        $printingServices = PrintingService::where('is_active', true)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->get();

        $technicalServices = TechnicalService::where('is_active', true)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->get();

        return view('customer.store', [
            'printingServices' => $printingServices,
            'technicalServices' => $technicalServices,
        ]);
    }

    public function requestService(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer',
            'service_type' => 'required|in:printing,technical',
            'notes' => 'nullable|string|max:1000',
        ]);

        $service = $validated['service_type'] === 'printing'
            ? PrintingService::findOrFail($validated['service_id'])
            : TechnicalService::findOrFail($validated['service_id']);

        $serviceJob = ServiceJob::create([
            'name' => $service->name,
            'description' => $service->description,
            'type' => $validated['service_type'],
            'customer_id' => auth()->id(),
            'service_id' => $service->id,
            'service_type' => $validated['service_type'] === 'printing' ? 'printing_service' : 'technical_service',
            'status' => null,
            'priority' => null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('customer.orders')->with('success', 'Service request submitted successfully!');
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

    public function destroyOrder(ServiceJob $order)
    {
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        $order->delete();

        return redirect()->route('customer.orders')->with('success', 'Order deleted successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        $hasUnverifiedEmail = $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();

        return view('customer.profile', [
            'user' => $user,
            'message' => 'lol',
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
