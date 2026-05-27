<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function conversations()
    {
        $user = Auth::user();
        $users = User::where('id', '!=', $user->id)->get();
        $conversations = [];

        foreach ($users as $u) {
            $lastMsg = ChatMessage::where(function ($q) use ($user, $u) {
                $q->where('sender_id', $user->id)->where('receiver_id', $u->id);
            })->orWhere(function ($q) use ($user, $u) {
                $q->where('sender_id', $u->id)->where('receiver_id', $user->id);
            })->latest()->first();

            $unread = ChatMessage::where('sender_id', $u->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();

            $conversations[] = [
                'user' => $u,
                'last_message' => $lastMsg?->message,
                'last_type' => $lastMsg?->type ?? 'text',
                'last_time' => $lastMsg?->created_at,
                'unread' => $unread,
            ];
        }

        usort($conversations, fn($a, $b) => ($b['last_time'] ?? null) <=> ($a['last_time'] ?? null));

        return response()->json($conversations);
    }

    public function messages($userId)
    {
        $user = Auth::user();

        ChatMessage::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = ChatMessage::where(function ($q) use ($user, $userId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($user, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required_without:file|string|max:2000',
            'type' => 'nullable|in:text,image,audio',
        ]);

        $type = $request->type ?? 'text';
        $message = $type === 'text' ? $request->message : '';

        $msg = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $message,
            'type' => $type,
        ]);

        return response()->json([
            'success' => true,
            'message' => $msg->load('sender'),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'file' => 'required|file|max:10240',
            'type' => 'required|in:image,audio',
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $folder = $request->type === 'image' ? 'chat/images' : 'chat/audio';
        $path = $file->storeAs($folder, $filename, 'public');

        $msg = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => '',
            'type' => $request->type,
            'file_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => $msg->load('sender'),
        ]);
    }

    public function unreadCount()
    {
        $count = ChatMessage::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}
