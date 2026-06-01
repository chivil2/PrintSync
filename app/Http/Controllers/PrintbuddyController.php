<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\PrintbuddyNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PrintbuddyController extends Controller
{
    /**
     * Handle incoming chat messages from PrintBuddy.
     *
     * Uses a JSON-based tool-calling protocol so it works with any Groq model
     * (not only those that support OpenAI-style function calling). The AI is
     * instructed to respond with one of:
     *   {"type":"response","message":"..."}
     *   {"type":"tool_call","tool":"<name>","args":{}}
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'conversation_id' => ['nullable', 'string'],
            'history' => ['nullable', 'array'],
        ]);

        // Use user's custom API key if set, otherwise fall back to config
        $userApiKey = auth()->user()->groq_api_key ?? null;
        $apiKey = $userApiKey ?: config('services.groq.api_key');
        $model = config('services.groq.model');
        $printbuddyApiKey = config('services.printbuddy.api_key');

        if (empty($apiKey)) {
            return response()->json([
                'error' => 'AI service not configured',
                'message' => 'GROQ API key not set. Please add your API key in Settings.',
            ], 500);
        }

        $availableTools = [
            'get_services' => 'Get all available printing and technical services (id, name, description, price, image, production_time, service_type).',
            'get_inventory' => 'Get all inventory items with current stock levels (id, name, sku, description, quantity, min_stock_level, unit_price, unit, supplier, location, status).',
            'get_employees' => 'Get all employees with status and details (id, name, email, position, status, hire_date).',
            'get_jobs' => 'Get all jobs with current status and details (id, name, description, type, status, priority, customer, assigned_to, started_at, completed_at, deadline).',
            'get_quotes' => 'Get all quotes with status and details (id, customer_name, total_amount, status).',
            'add_note' => 'Add a new note. Parameters: content (required, string), title (optional, string).',
            'get_notes' => 'Get all notes saved by the owner. Returns id, title, content, created_at.',
            'add_service' => 'Add a new printing or technical service. Parameters: name (required), description (required), price (required, number), service_type (required: printing or technical), production_time (optional).',
            'edit_service' => 'Edit an existing service. Parameters: id (required), name (optional), description (optional), price (optional), production_time (optional), is_active (optional).',
            'remove_service' => 'Remove a service. Parameters: id (required).',
            'add_inventory' => 'Add a new inventory item. Parameters: name (required), sku (required), description (optional), quantity (required, number), min_stock_level (optional), unit_price (optional), unit (optional), supplier (optional), location (optional).',
            'edit_inventory' => 'Edit an inventory item. Parameters: id (required), name (optional), sku (optional), description (optional), quantity (optional), min_stock_level (optional), unit_price (optional), unit (optional), supplier (optional), location (optional), status (optional).',
            'remove_inventory' => 'Remove an inventory item. Parameters: id (required).',
        ];

        $toolsList = collect($availableTools)
            ->map(fn ($desc, $name) => "- {$name}: {$desc}")
            ->implode("\n");

        $systemPrompt = "You are PrintBuddy, an AI assistant for a printing business management web app. You help the business owner manage services, pricing, inventory, employees, jobs, quotes, and their personal notes.

IMPORTANT RESTRICTIONS:
- Only answer questions related to this web app (services, pricing, inventory, employees, jobs, quotes, notes)
- If asked about anything else (general knowledge, math, coding, personal matters, etc.), politely decline and redirect to the web app
- Use Philippine Peso (₱) for prices
- When showing lists, show 5 items by default and note if more are available
- When adding/editing services or inventory, ALWAYS ask for the required details first before calling the tool. Do not assume values. Ask the user to provide: name, description, price, etc.

AVAILABLE TOOLS:
{$toolsList}

RESPOND ONLY WITH VALID JSON. Use one of these formats and nothing else:
1. To answer the user: {\"type\":\"response\",\"message\":\"<your reply to the user>\"}
2. To call ONE tool: {\"type\":\"tool_call\",\"tool\":\"<tool_name>\",\"args\":{}}
3. To call MULTIPLE tools at once: {\"type\":\"tool_calls\",\"tools\":[{\"tool\":\"<name>\",\"args\":{}},{\"tool\":\"<name>\",\"args\":{}}]}

Do not include any text, markdown, or code fences outside the JSON.";

        $notes = PrintbuddyNote::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        if ($notes->count() > 0) {
            $systemPrompt .= "\n\nOWNER NOTES:\n";
            foreach ($notes as $note) {
                $systemPrompt .= '- '.($note->title ? "[{$note->title}] " : '').$note->content."\n";
            }
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add compact history (last 10 messages to avoid token limits)
        $history = $validated['history'] ?? [];
        if (is_array($history)) {
            $compactHistory = array_slice($history, -10);
            foreach ($compactHistory as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = [
                        'role' => in_array($msg['role'], ['user', 'assistant']) ? $msg['role'] : 'user',
                        'content' => substr($msg['content'], 0, 500),
                    ];
                }
            }
        }

        // Add current message
        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        try {
            $maxIterations = 5;

            for ($i = 0; $i < $maxIterations; $i++) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ])->withoutVerifying()->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 800,
                ]);

                if (! $response->successful()) {
                    \Log::error('Groq API Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    return response()->json([
                        'error' => 'AI service error',
                        'message' => "Sorry, I'm having trouble connecting right now. Please try again.",
                        'conversation_id' => $validated['conversation_id'] ?? null,
                    ], 500);
                }

                $data = $response->json();
                $aiContent = trim((string) ($data['choices'][0]['message']['content'] ?? ''));

                $messages[] = ['role' => 'assistant', 'content' => $aiContent];

                $parsed = $this->parseJsonResponse($aiContent);

                if ($parsed === null) {
                    return response()->json([
                        'message' => $aiContent !== '' ? $aiContent : 'Sorry, I could not generate a response.',
                        'conversation_id' => $validated['conversation_id'] ?? null,
                    ]);
                }

                $type = $parsed['type'] ?? '';

                if ($type === 'response') {
                    return response()->json([
                        'message' => (string) ($parsed['message'] ?? ''),
                        'conversation_id' => $validated['conversation_id'] ?? null,
                    ]);
                }

                if ($type === 'tool_call') {
                    $toolName = (string) ($parsed['tool'] ?? '');
                    $toolArgs = is_array($parsed['args'] ?? null) ? $parsed['args'] : [];

                    if (! array_key_exists($toolName, $availableTools)) {
                        $messages[] = [
                            'role' => 'user',
                            'content' => "The tool '{$toolName}' does not exist. Available tools: ".implode(', ', array_keys($availableTools)).'. Respond with a normal message instead.',
                        ];

                        continue;
                    }

                    $toolResult = $this->executeTool($toolName, $toolArgs, $printbuddyApiKey);

                    $messages[] = [
                        'role' => 'user',
                        'content' => "Tool '{$toolName}' returned:\n".json_encode($toolResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n\nUse this data to answer the user. Respond with {\"type\":\"response\",\"message\":\"...\"}.",
                    ];

                    continue;
                }

                if ($type === 'tool_calls') {
                    $tools = $parsed['tools'] ?? [];
                    $toolResults = [];

                    foreach ($tools as $toolItem) {
                        $toolName = (string) ($toolItem['tool'] ?? '');
                        $toolArgs = is_array($toolItem['args'] ?? null) ? $toolItem['args'] : [];

                        if (! array_key_exists($toolName, $availableTools)) {
                            $toolResults[] = ['tool' => $toolName, 'error' => "Tool does not exist. Available: ".implode(', ', array_keys($availableTools))];
                            continue;
                        }

                        $toolResult = $this->executeTool($toolName, $toolArgs, $printbuddyApiKey);
                        $toolResults[] = ['tool' => $toolName, 'result' => $toolResult];
                    }

                    $messages[] = [
                        'role' => 'user',
                        'content' => "Tools executed:\n".json_encode($toolResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n\nUse this data to answer the user. Respond with {\"type\":\"response\",\"message\":\"...\"}.",
                    ];

                    continue;
                }

                return response()->json([
                    'message' => $aiContent,
                    'conversation_id' => $validated['conversation_id'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Sorry, I had trouble processing your request. Please try again.',
                'conversation_id' => $validated['conversation_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Groq API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'AI service error',
                'message' => 'Sorry, something went wrong. Please try again.',
                'conversation_id' => $validated['conversation_id'] ?? null,
            ], 500);
        }
    }

    /**
     * Try to parse an AI response as JSON, stripping markdown code fences.
     */
    private function parseJsonResponse(string $content): ?array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
        $content = preg_replace('/\s*```\s*$/', '', $content);
        $content = trim($content);

        $parsed = json_decode($content, true);

        return is_array($parsed) ? $parsed : null;
    }

    /**
     * Execute a tool function and return the result.
     */
    private function executeTool(string $functionName, array $functionArgs, string $apiKey): array
    {
        try {
            $result = match ($functionName) {
                'get_services' => $this->getServices()->getData(true),
                'get_inventory' => $this->getInventory()->getData(true),
                'get_employees' => $this->getEmployees()->getData(true),
                'get_jobs' => $this->getJobs()->getData(true),
                'get_quotes' => $this->getQuotes()->getData(true),
                'add_note' => $this->addNote($functionArgs),
                'get_notes' => $this->getNotes()->getData(true),
                'add_service' => $this->addService($functionArgs),
                'edit_service' => $this->editService($functionArgs),
                'remove_service' => $this->removeService($functionArgs),
                'add_inventory' => $this->addInventory($functionArgs),
                'edit_inventory' => $this->editInventory($functionArgs),
                'remove_inventory' => $this->removeInventory($functionArgs),
                default => ['error' => 'Unknown function'],
            };

            return $result;
        } catch (\Exception $e) {
            \Log::error('Tool execution error', [
                'function' => $functionName,
                'error' => $e->getMessage(),
            ]);

            return ['error' => 'Failed to execute tool: '.$e->getMessage()];
        }
    }

    /**
     * Get chat history for a conversation.
     */
    public function history(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation_id' => ['nullable', 'string'],
        ]);

        // TODO: Implement chat history retrieval
        // This will fetch previous messages in the conversation

        return response()->json([
            'messages' => [],
            'conversation_id' => $validated['conversation_id'] ?? null,
        ]);
    }

    /**
     * Clear chat history for a conversation.
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation_id' => ['nullable', 'string'],
        ]);

        // TODO: Implement chat history clearing
        // This will clear the conversation history

        return response()->json([
            'success' => true,
            'message' => 'Chat history cleared',
        ]);
    }

    /**
     * MCP: Get all services (printing and technical).
     */
    public function getServices(): JsonResponse
    {
        $printingServices = DB::table('printing_services')
            ->select('id', 'name', 'description', 'price', 'image', 'is_active', 'production_time')
            ->selectRaw("'printing' as service_type")
            ->get();

        $technicalServices = DB::table('technical_services')
            ->select('id', 'name', 'description', 'price', 'image', 'is_active', 'production_time')
            ->selectRaw("'technical' as service_type")
            ->get();

        $services = $printingServices->concat($technicalServices);

        return response()->json([
            'services' => $services,
        ]);
    }

    /**
     * MCP: Get all inventory items.
     */
    public function getInventory(): JsonResponse
    {
        $inventory = Inventory::all([
            'id', 'name', 'sku', 'description', 'quantity', 'min_stock_level',
            'unit_price', 'unit', 'supplier', 'location', 'status',
        ]);

        return response()->json([
            'inventory' => $inventory,
        ]);
    }

    /**
     * MCP: Get available tools/functions for PrintBuddy.
     */
    public function getTools(): JsonResponse
    {
        $tools = [
            [
                'name' => 'get_services',
                'description' => 'Get all available printing and technical services with their details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'get_inventory',
                'description' => 'Get all inventory items with their current stock levels and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'get_employees',
                'description' => 'Get all employees with their status and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'get_jobs',
                'description' => 'Get all jobs with their current status and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'get_quotes',
                'description' => 'Get all quotes with their status and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'name' => 'add_note',
                'description' => 'Add a new note for the owner. Parameters: content (required), title (optional)',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'content' => ['type' => 'string', 'description' => 'The note content'],
                        'title' => ['type' => 'string', 'description' => 'Optional title for the note'],
                    ],
                    'required' => ['content'],
                ],
            ],
            [
                'name' => 'get_notes',
                'description' => 'Get all notes saved by the owner',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
        ];

        return response()->json([
            'tools' => $tools,
        ]);
    }

    /**
     * MCP: Get all employees.
     */
    public function getEmployees(): JsonResponse
    {
        $employees = DB::table('users')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->select('users.id', 'users.name', 'users.email', 'employees.position', 'employees.status', 'employees.hire_date')
            ->get();

        return response()->json([
            'employees' => $employees,
        ]);
    }

    /**
     * MCP: Get all jobs.
     */
    public function getJobs(): JsonResponse
    {
        $jobs = DB::table('service_jobs')
            ->leftJoin('users as customers', 'service_jobs.customer_id', '=', 'customers.id')
            ->leftJoin('users as employees', 'service_jobs.employee_id', '=', 'employees.id')
            ->leftJoin('printing_services', function ($join) {
                $join->on('service_jobs.service_id', '=', 'printing_services.id')
                    ->where('service_jobs.service_type', '=', 'printing_service');
            })
            ->leftJoin('technical_services', function ($join) {
                $join->on('service_jobs.service_id', '=', 'technical_services.id')
                    ->where('service_jobs.service_type', '=', 'technical_service');
            })
            ->select(
                'service_jobs.id',
                'service_jobs.name',
                'service_jobs.description',
                'service_jobs.type',
                'service_jobs.status',
                'service_jobs.priority',
                'service_jobs.started_at',
                'service_jobs.completed_at',
                'service_jobs.deadline',
                'service_jobs.created_at',
                'service_jobs.updated_at',
                'customers.name as customer_name',
                'employees.name as assigned_to',
                DB::raw('COALESCE(printing_services.name, technical_services.name) as service_name')
            )
            ->orderBy('service_jobs.created_at', 'desc')
            ->get();

        return response()->json([
            'jobs' => $jobs,
        ]);
    }

    /**
     * Add a note via AI tool.
     */
    private function addNote(array $args): array
    {
        $content = $args['content'] ?? null;
        $title = $args['title'] ?? null;

        if (empty($content)) {
            return ['success' => false, 'error' => 'Content is required'];
        }

        PrintbuddyNote::create([
            'user_id' => auth()->id(),
            'content' => $content,
            'title' => $title ?? null,
        ]);

        return ['success' => true, 'message' => 'Note saved successfully'];
    }

    /**
     * Get all notes.
     */
    public function getNotes(): JsonResponse
    {
        $notes = PrintbuddyNote::where('user_id', auth()->id())
            ->select('id', 'title', 'content', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notes' => $notes,
        ]);
    }

    /**
     * Add a new service.
     */
    private function addService(array $args): array
    {
        $name = $args['name'] ?? null;
        $description = $args['description'] ?? null;
        $price = $args['price'] ?? null;
        $serviceType = $args['service_type'] ?? null;
        $productionTime = $args['production_time'] ?? null;

        if (empty($name) || empty($description) || empty($price) || empty($serviceType)) {
            return ['success' => false, 'error' => 'Missing required fields: name, description, price, service_type'];
        }

        $model = $serviceType === 'technical' ? \App\Models\TechnicalService::class : \App\Models\PrintingService::class;

        $id = $model::create([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'production_time' => $productionTime,
            'is_active' => true,
        ])->id;

        return ['success' => true, 'message' => "Service '{$name}' added successfully", 'id' => $id];
    }

    /**
     * Edit an existing service.
     */
    private function editService(array $args): array
    {
        $id = $args['id'] ?? null;
        if (empty($id)) {
            return ['success' => false, 'error' => 'Service ID is required'];
        }

        $updateData = [];
        if (isset($args['name'])) $updateData['name'] = $args['name'];
        if (isset($args['description'])) $updateData['description'] = $args['description'];
        if (isset($args['price'])) $updateData['price'] = $args['price'];
        if (isset($args['production_time'])) $updateData['production_time'] = $args['production_time'];
        if (isset($args['is_active'])) $updateData['is_active'] = $args['is_active'];

        if (empty($updateData)) {
            return ['success' => false, 'error' => 'No fields to update'];
        }

        // Try printing_services first, then technical_services
        $service = \App\Models\PrintingService::find($id);
        if (! $service) {
            $service = \App\Models\TechnicalService::find($id);
        }

        if (! $service) {
            return ['success' => false, 'error' => 'Service not found'];
        }

        $service->update($updateData);

        return ['success' => true, 'message' => 'Service updated successfully'];
    }

    /**
     * Remove a service.
     */
    private function removeService(array $args): array
    {
        $id = $args['id'] ?? null;
        if (empty($id)) {
            return ['success' => false, 'error' => 'Service ID is required'];
        }

        $deleted = \App\Models\PrintingService::destroy($id);
        if (! $deleted) {
            $deleted = \App\Models\TechnicalService::destroy($id);
        }

        if ($deleted) {
            return ['success' => true, 'message' => 'Service removed successfully'];
        }

        return ['success' => false, 'error' => 'Service not found'];
    }

    /**
     * Add a new inventory item.
     */
    private function addInventory(array $args): array
    {
        $name = $args['name'] ?? null;
        $sku = $args['sku'] ?? null;
        $quantity = $args['quantity'] ?? 0;

        if (empty($name) || empty($sku)) {
            return ['success' => false, 'error' => 'Missing required fields: name, sku'];
        }

        $id = Inventory::create([
            'name' => $name,
            'sku' => $sku,
            'description' => $args['description'] ?? null,
            'quantity' => $quantity,
            'min_stock_level' => $args['min_stock_level'] ?? 10,
            'unit_price' => $args['unit_price'] ?? 0,
            'unit' => $args['unit'] ?? null,
            'supplier' => $args['supplier'] ?? null,
            'location' => $args['location'] ?? null,
            'status' => 'in_stock',
        ])->id;

        return ['success' => true, 'message' => "Inventory item '{$name}' added successfully", 'id' => $id];
    }

    /**
     * Edit an inventory item.
     */
    private function editInventory(array $args): array
    {
        $id = $args['id'] ?? null;
        if (empty($id)) {
            return ['success' => false, 'error' => 'Inventory ID is required'];
        }

        $inventory = Inventory::find($id);
        if (! $inventory) {
            return ['success' => false, 'error' => 'Inventory item not found'];
        }

        if (isset($args['name'])) $inventory->name = $args['name'];
        if (isset($args['sku'])) $inventory->sku = $args['sku'];
        if (isset($args['description'])) $inventory->description = $args['description'];
        if (isset($args['quantity'])) $inventory->quantity = $args['quantity'];
        if (isset($args['min_stock_level'])) $inventory->min_stock_level = $args['min_stock_level'];
        if (isset($args['unit_price'])) $inventory->unit_price = $args['unit_price'];
        if (isset($args['unit'])) $inventory->unit = $args['unit'];
        if (isset($args['supplier'])) $inventory->supplier = $args['supplier'];
        if (isset($args['location'])) $inventory->location = $args['location'];
        if (isset($args['status'])) $inventory->status = $args['status'];

        $inventory->save();

        return ['success' => true, 'message' => 'Inventory item updated successfully'];
    }

    /**
     * Remove an inventory item.
     */
    private function removeInventory(array $args): array
    {
        $id = $args['id'] ?? null;
        if (empty($id)) {
            return ['success' => false, 'error' => 'Inventory ID is required'];
        }

        $deleted = Inventory::destroy($id);

        if ($deleted) {
            return ['success' => true, 'message' => 'Inventory item removed successfully'];
        }

        return ['success' => false, 'error' => 'Inventory item not found'];
    }

    /**
     * MCP: Get all quotes.
     */
    public function getQuotes(): JsonResponse
    {
        $quotes = DB::table('quotes')
            ->select('id', 'customer_name', 'total_amount', 'status', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'quotes' => $quotes,
        ]);
    }

    /**
     * Show PrintBuddy page.
     */
    public function index()
    {
        $notes = PrintbuddyNote::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.printbuddy', [
            'notes' => $notes,
        ]);
    }

    /**
     * Store a new note.
     */
    public function storeNote(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        PrintbuddyNote::create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'title' => $validated['title'] ?? null,
        ]);

        return redirect()->route('owner.printbuddy')->with('success', 'Note saved.');
    }

    /**
     * Delete a note.
     */
    public function destroyNote(PrintbuddyNote $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        $note->delete();

        return redirect()->route('owner.printbuddy')->with('success', 'Note deleted.');
    }

    /**
     * Save user's API key.
     */
    public function saveApiKey(Request $request)
    {
        $validated = $request->validate([
            'groq_api_key' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $user->groq_api_key = $validated['groq_api_key'];
        $user->save();

        return redirect()->route('owner.printbuddy')->with('success', 'API key saved.');
    }
}
