<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get printing services
        $printingServices = DB::table('printing_services')
            ->select('id', 'name', 'description', 'price', 'image', 'is_active', 'created_at', 'updated_at', 'production_time')
            ->selectRaw("'printing' as service_type");

        // Get technical services
        $technicalServices = DB::table('technical_services')
            ->select('id', 'name', 'description', 'price', 'image', 'is_active', 'created_at', 'updated_at', 'production_time')
            ->selectRaw("'technical' as service_type");

        // Combine both queries
        $combinedQuery = $printingServices->union($technicalServices);

        // Build the final query with filters
        $sql = $combinedQuery->toSql();
        $bindings = $combinedQuery->getBindings();

        // Apply search filter if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $sql .= " WHERE name LIKE ?";
            $bindings = array_merge($bindings, ["%{$search}%"]);
        }

        // Apply service type filter if provided
        if ($request->filled('service_type')) {
            $operator = $request->filled('search') ? 'AND' : 'WHERE';
            $sql .= " {$operator} service_type = ?";
            $bindings[] = $request->service_type;
        }

        // Add ordering
        $sql .= " ORDER BY created_at DESC";

        // Execute the query and paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as combined";
        $total = DB::selectOne($countSql, $bindings)->total;

        // Get paginated results
        $paginatedSql = $sql . " LIMIT $perPage OFFSET $offset";
        $services = DB::select($paginatedSql, $bindings);

        // Convert to paginator
        $services = new \Illuminate\Pagination\LengthAwarePaginator(
            $services,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get service types for filter
        $serviceTypes = ['printing', 'technical'];

        return view('owner.services', [
            'services' => $services,
            'serviceTypes' => $serviceTypes,
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('owner.services-create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => ['required', 'in:printing,technical'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'production_time' => ['nullable', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $table = $validated['service_type'] === 'printing' ? 'printing_services' : 'technical_services';

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('service-images', 'public');
        }

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'production_time' => $validated['production_time'] ?? 7,
            'is_active' => $validated['is_active'] ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Add image if provided
        if ($imagePath) {
            $data['image'] = $imagePath;
        }

        DB::table($table)->insert($data);

        return redirect()->route('owner.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit($id, $serviceType)
    {
        $table = $serviceType === 'printing' ? 'printing_services' : 'technical_services';
        $service = DB::table($table)->where('id', $id)->first();

        if (!$service) {
            abort(404);
        }

        $service->service_type = $serviceType;

        return view('owner.services-edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, $id, $serviceType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'production_time' => ['nullable', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $table = $serviceType === 'printing' ? 'printing_services' : 'technical_services';

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            $existingService = DB::table($table)->where('id', $id)->first();
            if ($existingService && $existingService->image) {
                Storage::disk('public')->delete($existingService->image);
            }
            $imagePath = $request->file('image')->store('service-images', 'public');
        }

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'production_time' => $validated['production_time'] ?? 7,
            'is_active' => $validated['is_active'] ?? true,
            'updated_at' => now(),
        ];

        // Add image if provided
        if ($imagePath) {
            $data['image'] = $imagePath;
        }

        DB::table($table)->where('id', $id)->update($data);

        return redirect()->route('owner.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy($id, $serviceType)
    {
        $table = $serviceType === 'printing' ? 'printing_services' : 'technical_services';
        
        // Get service to delete image
        $service = DB::table($table)->where('id', $id)->first();
        
        if (!$service) {
            return redirect()->route('owner.services.index')
                ->with('error', 'Service not found.');
        }

        // Delete image if exists
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        // Delete the service
        DB::table($table)->where('id', $id)->delete();

        return redirect()->route('owner.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
