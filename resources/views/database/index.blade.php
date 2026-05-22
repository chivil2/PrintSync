<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Tables</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-white">Database Tables</h1>

        @foreach($tables as $table)
            <div class="mb-8 bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                <div class="bg-gray-700 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-white">{{ $table['name'] }}</h2>
                        <div class="text-sm text-gray-400 mt-1">
                            <span>ID: {{ $table['id'] }}</span> |
                            <span>Database: {{ $table['database'] }}</span> |
                            <span>Status: {{ $table['status'] }}</span> |
                            <span>Rows: {{ $table['count'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-750">
                            <tr>
                                @foreach($table['columns'] as $column)
                                    <th class="px-6 py-3 text-xs font-medium text-gray-300 uppercase tracking-wider border-b border-gray-600">{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($table['data'] as $row)
                                <tr class="hover:bg-gray-750">
                                    @foreach($table['columns'] as $column)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $row->$column ?? 'NULL' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($table['count'] > 100)
                    <div class="px-6 py-3 bg-gray-750 text-sm text-gray-400">
                        Showing first 100 of {{ $table['count'] }} rows
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>
