<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Notifications\NewChatMessageNotification;
use Illuminate\Http\Request;

class OwnerChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['customer', 'quote'])
            ->where('owner_id', auth()->id())
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get()
            ->each(function (Conversation $c) {
                $c->unread = $c->unreadCountFor(auth()->user());
            });

        return view('owner.chat.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['customer', 'quote', 'messages.sender']);
        $conversation->markReadBy(auth()->user());

        return view('owner.chat.show', compact('conversation'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorize('reply', $conversation);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        $conversation->forceFill(['last_message_at' => $message->created_at])->save();
        $conversation->markReadBy(auth()->user());

        $recipient = $conversation->customer_id === auth()->id()
            ? $conversation->owner
            : $conversation->customer;

        if ($recipient) {
            $recipient->notify(new NewChatMessageNotification(
                $conversation,
                mb_substr($message->body, 0, 80)
            ));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message->load('sender'),
            ], 201);
        }

        return redirect()->route('owner.chat.show', $conversation);
    }

    public function markRead(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $conversation->markReadBy(auth()->user());

        return response()->json(['ok' => true]);
    }

    public function poll(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $lastModified = $conversation->updated_at?->setMicrosecond(0);

        if ($request->header('If-Modified-Since') && $lastModified) {
            $ifModifiedSince = strtotime($request->header('If-Modified-Since'));
            if ($ifModifiedSince && $ifModifiedSince >= $lastModified->getTimestamp()) {
                return response()->json(null, 304);
            }
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->name,
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        $conversation->markReadBy(auth()->user());

        return response()->json([
            'messages' => $messages,
            'last_modified' => now()->toIso8601String(),
        ])->header('Last-Modified', $lastModified?->toRfc7231String() ?? '');
    }
}
