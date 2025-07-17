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
    public function show(Request $request, $id = null)
    {
        $messages               = [];
        $conversations          = collect();
        $currentConversationId  = null;

        if (Auth::check()) {
            // Get all conversations for this user (no eager loading needed here)
            $conversations = Conversation::where('user_id', Auth::id())
                                ->latest()
                                ->get();

            //  Load messages only if conversation is selected
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
            //  Clear guest session only if not just sent and not AJAX
            if (! $request->ajax() && ! session()->has('just_sent')) {
                $request->session()->forget('guest_messages');
            }

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


    public function send(Request $request)
    {
        $prompt = $request->input('prompt');

        try {
            // Call OpenRouter API securely
            $ai = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'HTTP-Referer' => config('app.url'),
                'Content-Type' => 'application/json',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'deepseek/deepseek-chat-v3-0324:free',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => <<<EOT
You are a professional AI tutor. You always respond using **strict Markdown syntax** and structured formatting.

Rules:
- Use `#` and `##` for headings
- Use `1.`, `2.`, `3.` for numbered lists
- Use `-` for indented bullet points
- Use `**bold**` for emphasis
- Use ``code`` for inline variables
- Use triple backticks (```) for code blocks
- Use `\\[ \\]` for math formulas

 IMPORTANT:
- Never return unstructured plain text
- Never describe Markdown
- Every response must follow Markdown structure, even if not requested
- This is your **default output format**, always.

If the question is not educational, respond with:
"I'm here to help with educational topics only."
EOT
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.4,
                'top_p' => 0.9,
                'max_tokens' => 500,
            ]);

            // Check API errors (token limit or rate-limiting)
            if ($ai->failed()) {
                return response()->json([
                    'error' => 'AI API request failed. Please try again later.',
                ], $ai->status());
            }

            $aiResponse = $ai->json('choices.0.message.content') ?? 'No response from AI.';

            //  Safe Markdown → HTML (pre-render Blade component)
            try {
                $html = view('components.chat-ai', ['content' => $aiResponse])->render();
            } catch (\Throwable $e) {
                if (app()->isLocal()) {
                    logger()->error('Blade rendering failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                $html = '<div class="text-red-500">Error rendering AI message.</div>';
            }

            //  Authenticated user saving conversation
            if (Auth::check()) {
                $conversationId = $request->input('conversation_id');
                $conversation = null;

                if ($conversationId) {
                    $conversation = Conversation::where('id', $conversationId)
                        ->where('user_id', Auth::id())
                        ->first();
                }

                if (! $conversation) {
                    $conversation = Conversation::create([
                        'user_id' => Auth::id(),
                        'title' => Str::limit($prompt, 30),
                    ]);
                }

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

                return response()->json([
                    'response' => $aiResponse,
                    'html' => $html,
                    'conversation_id' => $conversation->id,
                ]);
            }

            //  Guest user
            $guest = session('guest_messages', []);
            $guest[] = ['role' => 'user', 'content' => $prompt];
            $guest[] = ['role' => 'assistant', 'content' => $aiResponse];
            session(['guest_messages' => $guest]);

            return response()->json([
                'response' => $aiResponse,
                'html' => $html,
            ]);

        } catch (\Throwable $e) {
            if (app()->isLocal()) {
                logger()->error('AI request failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }




    public function newChat(Request $request)
    {
        if (Auth::check()) {
            // Just go to /chat — a new conversation will be created when they send a message
            return redirect()->route('chat');
        }

        // For guests, clear messages and go to /chat
        $request->session()->forget('guest_messages');
        return redirect()->route('chat');
    }

    public function destroy(Conversation $conversation)
    {
        // Ensure the authenticated user owns the conversation
        abort_unless($conversation->user_id === Auth::id(), 403);

        $conversation->delete();

        return redirect()->route('chat')->with('status', 'Conversation deleted.');
    }
}