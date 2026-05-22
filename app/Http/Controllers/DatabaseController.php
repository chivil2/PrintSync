<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseController extends Controller
{
    public function index()
    {
        $tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"))
            ->pluck('name')
            ->map(function ($tableName) {
                $columns = Schema::getColumnListing($tableName);
                $data = DB::table($tableName)->limit(100)->get();

                return [
                    'name' => $tableName,
                    'id' => $tableName,
                    'database' => config('database.connections.sqlite.database'),
                    'status' => 'active',
                    'columns' => $columns,
                    'data' => $data,
                    'count' => DB::table($tableName)->count(),
                ];
            });

        return view('database.index', compact('tables'));
    }
}
