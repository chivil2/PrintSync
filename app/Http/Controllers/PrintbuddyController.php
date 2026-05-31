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

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are PrintBuddy, a helpful AI assistant for a printing business. You help customers with printing services, pricing, inventory questions, and general business inquiries. Be friendly, professional, and concise.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $validated['message'],
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiMessage = $data['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';

                return response()->json([
                    'message' => $aiMessage,
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
}
