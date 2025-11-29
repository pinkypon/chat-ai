<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    /**
     * Display chat view with conversations and messages.
     */
    public function show(Request $request, $id = null)
    {
        $messages               = [];
        $conversations          = collect();
        $currentConversationId  = null;

        if (Auth::check()) {
            // Fetch all conversations for the logged-in user
            $conversations = Conversation::where('user_id', Auth::id())
                                ->latest()
                                ->get();

            // Load selected conversation and its messages if ID is provided
            if ($id) {
                $conversation = Conversation::with(['messages' => function ($query) {
                    $query->orderBy('created_at');
                }])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

                if ($conversation) {
                    $currentConversationId = $conversation->id;
                    $messages = $conversation->messages
                        ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
                        ->toArray();
                }
            }
        } else {
            // For guests: reset session messages if not an AJAX request
            if (! $request->ajax() && ! session()->has('just_sent')) {
                $request->session()->forget('guest_messages');
            }

            // Load guest messages from session
            $messages = session('guest_messages', []);
        }

        return view('chat', [
            'messages'              => $messages,
            'conversations'         => $conversations,
            'currentConversationId' => $currentConversationId,
        ]);
    }

        // public function send(Request $request)
    // {
    //     $prompt = $request->input('prompt');

    //     //  Call your local AI API
    //     // $ai = Http::post(env('AI_API_URL'), [
    //     //     'prompt' => $prompt,
    //     // ]);
    //     $ai = Http::post('http://127.0.0.1:5050/generate', [
    //         'prompt' => $prompt,
    //     ]);
    //     $aiResponse = $ai->json()['response'] ?? 'No response from AI.';

    //     if (Auth::check()) {
    //         $conversationId = $request->input('conversation_id');
    //         $conversation = null;

    //         if ($conversationId) {
    //             $conversation = Conversation::where('id', $conversationId)
    //                 ->where('user_id', Auth::id())
    //                 ->first();
    //         }

    //         if (! $conversation) {
    //             $conversation = Conversation::create([
    //                 'user_id' => Auth::id(),
    //                 'title'   => Str::limit($prompt, 30),
    //             ]);
    //         }

    //         Message::create([
    //             'conversation_id' => $conversation->id,
    //             'role'            => 'user',
    //             'content'         => $prompt,
    //         ]);
    //         Message::create([
    //             'conversation_id' => $conversation->id,
    //             'role'            => 'assistant',
    //             'content'         => $aiResponse,
    //         ]);

    //         // Return JSON instead of redirect
    //         return response()->json([
    //             'response' => $aiResponse,
    //             'conversation_id' => $conversation->id,
    //         ]);
    //     }

    //     //  Guest logic
    //     $guest = session('guest_messages', []);
    //     $guest[] = ['role' => 'user', 'content' => $prompt];
    //     $guest[] = ['role' => 'assistant', 'content' => $aiResponse];
    //     session(['guest_messages' => $guest]);

    //     return response()->json([
    //         'response' => $aiResponse,
    //     ]);
    // }

    /**
     * Handle sending a message to the AI API and saving the conversation.
     */
public function send(Request $request)
{
    $prompt = $request->input('prompt');

    try {
        // Send user prompt to Google Gemini API
        $ai = Http::timeout(30)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=' . env('GEMINI_API_KEY'), [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "You are a professional AI tutor. You always respond using **strict Markdown syntax** and structured formatting.\n\nRules:\n- Use `#` and `##` for headings\n- Use `1.`, `2.`, `3.` for numbered lists\n- Use `-` for indented bullet points\n- Use `**bold**` for emphasis\n- Use `code` for inline variables\n- Use triple backticks (```) for code blocks\n- Use \\[ \\] for math formulas\n\nIMPORTANT:\n- Never return unstructured plain text\n- Never describe Markdown\n- Every response must follow Markdown structure, even if not requested\n- This is your **default output format**, always.\n\nIf the question is not educational, respond with:\n\"I'm here to help with educational topics only.\"\n\nUser question: " . $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'topP' => 0.9,
                    // 'maxOutputTokens' => 500, for testing
                    'maxOutputTokens' => 2048,
                ]
            ]);

        // Enhanced error handling for API failures
        if ($ai->failed()) {
            $statusCode = $ai->status();
            $errorBody = $ai->json();
            $errorMessage = 'AI API request failed.';

            // Extract specific error message
            if (isset($errorBody['error']['message'])) {
                $errorMessage = $errorBody['error']['message'];
            }

            logger()->error('Gemini API Error', [
                'status' => $statusCode,
                'error_body' => $errorBody,
                'raw_body' => $ai->body(),
            ]);

            return response()->json([
                'error' => app()->isLocal() 
                    ? "API Error ({$statusCode}): {$errorMessage}" 
                    : 'AI service is currently unavailable. Please try again later.',
                'debug' => app()->isLocal() ? $errorBody : null,
            ], $statusCode);
        }

        // Extract AI response text
        $aiResponse = $ai->json('candidates.0.content.parts.0.text');
        
        if (empty($aiResponse)) {
            logger()->warning('Empty AI response received', [
                'full_response' => $ai->json(),
            ]);
            $aiResponse = 'No response from AI. Please try again.';
        }

        // Clean up Gemini's response - remove code block wrappers if present
        // Remove opening ```markdown or ``` at start
        $aiResponse = preg_replace('/^\s*```(?:markdown)?\s*\n/i', '', $aiResponse);
        // Remove closing ``` at end
        $aiResponse = preg_replace('/\n\s*```\s*$/i', '', $aiResponse);
        // Trim any extra whitespace
        $aiResponse = trim($aiResponse);

        // Render AI response into Blade component
        try {
            $html = view('components.chat-ai', ['content' => $aiResponse])->render();
        } catch (\Throwable $e) {
            logger()->error('Blade rendering failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $html = '<div class="text-red-500">Error rendering AI message.</div>';
            
            if (app()->isLocal()) {
                $html .= '<div class="text-sm text-gray-600 mt-2">Debug: ' . e($e->getMessage()) . '</div>';
            }
        }

        // Save conversation and messages for logged-in users
        if (Auth::check()) {
            $conversationId = $request->input('conversation_id');
            $conversation = null;

            if ($conversationId) {
                $conversation = Conversation::where('id', $conversationId)
                    ->where('user_id', Auth::id())
                    ->first();
            }
            
            if (!$conversation) {
                try {
                    $conversation = Conversation::create([
                        'user_id' => Auth::id(),
                        'title' => Str::limit($prompt, 30),
                    ]);
                } catch (\Throwable $e) {
                    logger()->error('Failed to create conversation', [
                        'error' => $e->getMessage(),
                        'user_id' => Auth::id(),
                    ]);
                    
                    return response()->json([
                        'error' => 'Failed to save conversation. Please try again.',
                    ], 500);
                }
            }

            try {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'user',
                    'content' => $prompt,
                ]);
                
                Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $aiResponse,
                ]);
            } catch (\Throwable $e) {
                logger()->error('Failed to save messages', [
                    'error' => $e->getMessage(),
                    'conversation_id' => $conversation->id,
                ]);
            }

            return response()->json([
                'response' => $aiResponse,
                'html' => $html,
                'conversation_id' => $conversation->id,
            ]);
        }

        // Guest user: store messages in session
        try {
            $guest = session('guest_messages', []);
            $guest[] = ['role' => 'user', 'content' => $prompt];
            $guest[] = ['role' => 'assistant', 'content' => $aiResponse];
            session(['guest_messages' => $guest]);
        } catch (\Throwable $e) {
            logger()->warning('Failed to save guest messages to session', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'response' => $aiResponse,
            'html' => $html,
        ]);

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        logger()->error('Network connection failed', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'error' => 'Network connection failed. Please check your internet connection.',
            'debug' => app()->isLocal() ? $e->getMessage() : null,
        ], 503);
        
    } catch (\Throwable $e) {
        logger()->error('AI request failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Something went wrong. Please try again later.',
            'debug' => app()->isLocal() ? [
                'message' => $e->getMessage(),
                'type' => get_class($e),
            ] : null,
        ], 500);
    }
}
    /**
     * Start a new chat session.
     */
    public function newChat(Request $request)
    {
        if (Auth::check()) {
            // Authenticated: redirect to chat, new conversation will start on first message
            return redirect()->route('chat');
        }

        // Guests: clear session messages and redirect to chat
        $request->session()->forget('guest_messages');
        return redirect()->route('chat');
    }

    /**
     * Delete a conversation (only if it belongs to the user).
     */
    public function destroy(Conversation $conversation)
    {
        // Ensure conversation belongs to the logged-in user
        abort_unless($conversation->user_id === Auth::id(), 403);

        $conversation->delete();

        return redirect()->route('chat')->with('status', 'Conversation deleted.');
    }
}
