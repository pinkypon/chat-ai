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

            // ✅ Load messages only if conversation is selected
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
            // ✅ Clear guest session only if not just sent and not AJAX
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





    public function send(Request $request)
    {
        $prompt = $request->input('prompt');

        // 🔸 Call your local AI API
        $ai = Http::post('http://127.0.0.1:5050/generate', [
            'prompt' => $prompt,
        ]);
        $aiResponse = $ai->json()['response'] ?? 'No response from AI.';

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
                    'title'   => Str::limit($prompt, 30),
                ]);
            }

            Message::create([
                'conversation_id' => $conversation->id,
                'role'            => 'user',
                'content'         => $prompt,
            ]);
            Message::create([
                'conversation_id' => $conversation->id,
                'role'            => 'assistant',
                'content'         => $aiResponse,
            ]);

            // ✅ Return JSON instead of redirect
            return response()->json([
                'response' => $aiResponse,
                'conversation_id' => $conversation->id,
            ]);
        }

        // 🔸 Guest logic
        $guest = session('guest_messages', []);
        $guest[] = ['role' => 'user', 'content' => $prompt];
        $guest[] = ['role' => 'assistant', 'content' => $aiResponse];
        session(['guest_messages' => $guest]);

        return response()->json([
            'response' => $aiResponse,
        ]);
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