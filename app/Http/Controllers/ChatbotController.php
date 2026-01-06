<?php

namespace App\Http\Controllers;

use App\Services\AIServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot message
     */
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);

            $message = $request->input('message');

            // Get AI service
            $aiService = AIServiceFactory::create();

            // Get AI response (no conversation history)
            $response = $aiService->chat($message);

            return response()->json([
                'success' => true,
                'message' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => "I'm sorry, something went wrong. Please try again later."
            ], 500);
        }
    }
}

