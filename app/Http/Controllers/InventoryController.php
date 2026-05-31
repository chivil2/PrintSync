<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventory = Inventory::all();

        return view('owner.inventory', [
            'inventory' => $inventory,
        ]);
    }

    public function create()
    {
        return view('owner.inventory-create');
    }

    public function store(Request $request)
    {
        //
    }

    public function edit($id)
    {
        return view('owner.inventory-edit');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
