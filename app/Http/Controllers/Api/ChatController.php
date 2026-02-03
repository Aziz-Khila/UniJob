<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get all conversations for the authenticated user.
     */
    public function conversations()
    {
        $user = Auth::user();
        
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['user1', 'user2', 'latestMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                $otherUser = $conversation->getOtherUser($user->id);
                $unreadCount = $conversation->getUnreadCount($user->id);
                $latestMessage = $conversation->latestMessage;

                return [
                    'id' => $conversation->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                        'role' => $otherUser->role,
                        'profile_picture' => $otherUser->profile_picture,
                    ],
                    'latest_message' => $latestMessage ? [
                        'id' => $latestMessage->id,
                        'message' => $latestMessage->message,
                        'sender_id' => $latestMessage->sender_id,
                        'sender_name' => $latestMessage->sender->name,
                        'created_at' => $latestMessage->created_at->toISOString(),
                    ] : null,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conversation->last_message_at?->toISOString(),
                    'created_at' => $conversation->created_at->toISOString(),
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }

    /**
     * Get available users to start a conversation with.
     */
    public function availableUsers()
    {
        $user = Auth::user();
        $availableUsers = ChatService::getAvailableUsers($user->id);

        // Ensure we have a collection
        if (!$availableUsers instanceof \Illuminate\Support\Collection) {
            $availableUsers = collect($availableUsers);
        }

        $users = $availableUsers->map(function ($availableUser) use ($user) {
            // Check if conversation already exists
            $conversation = Conversation::where(function ($query) use ($user, $availableUser) {
                $query->where('user1_id', $user->id)
                      ->where('user2_id', $availableUser->id);
            })->orWhere(function ($query) use ($user, $availableUser) {
                $query->where('user1_id', $availableUser->id)
                      ->where('user2_id', $user->id);
            })->first();

            return [
                'id' => $availableUser->id,
                'name' => $availableUser->name,
                'email' => $availableUser->email,
                'role' => $availableUser->role,
                'profile_picture' => $availableUser->profile_picture,
                'conversation_id' => $conversation?->id,
            ];
        })->values(); // Reset array keys

        // Log for debugging
        \Log::info('Available users for user ' . $user->id . ' (' . $user->role . '):', [
            'count' => $users->count(),
            'users' => $users->toArray()
        ]);

        // Convert collection to array for JSON response
        return response()->json([
            'users' => $users->toArray()
        ]);
    }

    /**
     * Get messages for a specific conversation.
     */
    public function messages(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Verify user is part of this conversation
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $perPage = $request->input('per_page', 10);
        $beforeId = $request->input('before_id');
        
        if ($beforeId) {
            // Loading older messages (messages before the oldest one currently displayed)
            $beforeMessage = \App\Models\Message::find($beforeId);
            if ($beforeMessage && $beforeMessage->conversation_id === $conversation->id) {
                // Get messages created before this message, ordered oldest first
                $messages = $conversation->messages()
                    ->with('sender')
                    ->where(function($q) use ($beforeMessage) {
                        $q->where('created_at', '<', $beforeMessage->created_at)
                          ->orWhere(function($q2) use ($beforeMessage) {
                              $q2->where('created_at', '=', $beforeMessage->created_at)
                                 ->where('id', '<', $beforeMessage->id);
                          });
                    })
                    ->orderBy('created_at', 'desc') // Get newest of older messages first
                    ->take($perPage + 1)
                    ->get()
                    ->reverse() // Reverse to show oldest first
                    ->values();
            } else {
                $messages = collect([]);
            }
        } else {
            // Initial load - get the last N messages (newest N messages)
            // Return them ordered oldest first, newest last
            $totalCount = $conversation->messages()->count();
            $offset = max(0, $totalCount - $perPage);
            
            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc') // Order all messages oldest first
                ->skip($offset) // Skip to get the last N messages
                ->take($perPage + 1) // Take one extra to check if there are more
                ->get();
        }
        
        // Check if we got an extra message (to determine if there are more)
        $gotExtra = $messages->count() > $perPage;
        if ($gotExtra) {
            $messages = $messages->take($perPage); // Remove the extra message
        }
        
        // Determine if there are more older messages
        $hasMore = false;
        if ($messages->count() > 0) {
            $firstMessage = $messages->first(); // Oldest message in the returned set
            $hasMore = $conversation->messages()
                ->where(function($q) use ($firstMessage) {
                    $q->where('created_at', '<', $firstMessage->created_at)
                      ->orWhere(function($q2) use ($firstMessage) {
                          $q2->where('created_at', '=', $firstMessage->created_at)
                             ->where('id', '<', $firstMessage->id);
                      });
                })
                ->exists();
        }
        
        $messages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'profile_picture' => $message->sender->profile_picture,
                ],
                'read_at' => $message->read_at?->toISOString(),
                'created_at' => $message->created_at->toISOString(),
            ];
        })
        ->values(); // Reset keys for JSON

        return response()->json([
            'data' => $messages,
            'has_more' => $hasMore
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // Verify user is part of this conversation
        if ($conversation->user1_id !== $user->id && $conversation->user2_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        // Update conversation's last_message_at
        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'profile_picture' => $message->sender->profile_picture,
                ],
                'read_at' => $message->read_at?->toISOString(),
                'created_at' => $message->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * Create a new conversation with a user.
     */
    public function createConversation(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otherUserId = $request->user_id;

        // Check if users can chat
        if (!ChatService::canChat($user->id, $otherUserId)) {
            return response()->json(['error' => 'You cannot start a conversation with this user'], 403);
        }

        // Get or create conversation
        $conversation = ChatService::getOrCreateConversation($user->id, $otherUserId);
        $otherUser = $conversation->getOtherUser($user->id);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'role' => $otherUser->role,
                    'profile_picture' => $otherUser->profile_picture,
                ],
                'created_at' => $conversation->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * Get unread messages count.
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->get();

        $totalUnread = 0;
        foreach ($conversations as $conversation) {
            $totalUnread += $conversation->getUnreadCount($user->id);
        }

        return response()->json(['count' => $totalUnread]);
    }
}
