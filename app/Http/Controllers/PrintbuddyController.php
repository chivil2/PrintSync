<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PrintbuddyController extends Controller
{
    /**
     * Handle incoming chat messages from PrintBuddy.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'conversation_id' => ['nullable', 'string'],
        ]);

        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.model');
        $printbuddyApiKey = config('services.printbuddy.api_key');

        // Define available tools for function calling
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_services',
                    'description' => 'Get all available printing and technical services with their details',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_inventory',
                    'description' => 'Get all inventory items with their current stock levels and details',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_employees',
                    'description' => 'Get all employees with their status and details',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_jobs',
                    'description' => 'Get all jobs with their current status and details',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_quotes',
                    'description' => 'Get all quotes with their status and details',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object)[],
                    ],
                ],
            ],
        ];

        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'You are PrintBuddy, a helpful AI assistant for a printing business. You are talking with the business owner and your role is to assist them with managing their printing business. Help with printing services, pricing, inventory questions, employee management, customer inquiries, and general business operations. Be friendly, professional, and concise. Remember that you are the assistant and the user is the owner.

When presenting lists (such as services, inventory items, or any enumerated data):
- Show only 5 items by default
- If the user asks for more, you can show up to 10 items maximum
- Always indicate if there are more items available beyond what you show
- Use Markdown formatting for lists and other structured content
- We are using Php Currency
You have access to tools to get real-time data about services, inventory, employees, jobs, and quotes. Use these tools when the user asks for information about these topics.',
                ],
                [
                    'role' => 'user',
                    'content' => $validated['message'],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'tools' => $tools,
                'tool_choice' => 'auto',
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiMessage = $data['choices'][0]['message'] ?? null;

                // Check if the AI wants to call a tool
                if (isset($aiMessage['tool_calls'])) {
                    // Execute the tool calls
                    $toolResponses = [];
                    foreach ($aiMessage['tool_calls'] as $toolCall) {
                        $functionName = $toolCall['function']['name'];
                        $functionArgs = json_decode($toolCall['function']['arguments'] ?? '{}', true);

                        $toolResult = $this->executeTool($functionName, $functionArgs, $printbuddyApiKey);
                        $toolResponses[] = [
                            'tool_call_id' => $toolCall['id'],
                            'role' => 'tool',
                            'content' => json_encode($toolResult),
                        ];
                    }

                    // Add the assistant message with tool calls and tool responses to the conversation
                    $messages[] = $aiMessage;
                    $messages = array_merge($messages, $toolResponses);

                    // Get the final response from the AI
                    $finalResponse = Http::withHeaders([
                        'Authorization' => 'Bearer '.$apiKey,
                        'Content-Type' => 'application/json',
                    ])->withoutVerifying()->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => $model,
                        'messages' => $messages,
                        'temperature' => 0.7,
                        'max_tokens' => 500,
                    ]);

                    if ($finalResponse->successful()) {
                        $finalData = $finalResponse->json();
                        $finalMessage = $finalData['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';

                        return response()->json([
                            'message' => $finalMessage,
                            'conversation_id' => $validated['conversation_id'] ?? null,
                        ]);
                    }
                }

                // If no tool calls, return the direct message
                $messageContent = $aiMessage['content'] ?? 'Sorry, I could not generate a response.';

                return response()->json([
                    'message' => $messageContent,
                    'conversation_id' => $validated['conversation_id'] ?? null,
                ]);
            }

            // Log the error for debugging
            \Log::error('Groq API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'Failed to get response from AI service',
                'message' => 'Sorry, I\'m having trouble connecting right now. Please try again.',
                'conversation_id' => $validated['conversation_id'] ?? null,
            ], 500);
        } catch (\Exception $e) {
            // Log the exception for debugging
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
     * Execute a tool function and return the result.
     */
    private function executeTool(string $functionName, array $functionArgs, string $apiKey): array
    {
        try {
            $result = match($functionName) {
                'get_services' => $this->getServices()->getData(true),
                'get_inventory' => $this->getInventory()->getData(true),
                'get_employees' => $this->getEmployees()->getData(true),
                'get_jobs' => $this->getJobs()->getData(true),
                'get_quotes' => $this->getQuotes()->getData(true),
                default => ['error' => 'Unknown function'],
            };

            return $result;
        } catch (\Exception $e) {
            \Log::error('Tool execution error', [
                'function' => $functionName,
                'error' => $e->getMessage(),
            ]);

            return ['error' => 'Failed to execute tool: ' . $e->getMessage()];
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
                    'properties' => (object)[],
                ],
            ],
            [
                'name' => 'get_inventory',
                'description' => 'Get all inventory items with their current stock levels and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object)[],
                ],
            ],
            [
                'name' => 'get_employees',
                'description' => 'Get all employees with their status and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object)[],
                ],
            ],
            [
                'name' => 'get_jobs',
                'description' => 'Get all jobs with their current status and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object)[],
                ],
            ],
            [
                'name' => 'get_quotes',
                'description' => 'Get all quotes with their status and details',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object)[],
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
}
