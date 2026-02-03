<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    /**
     * Get users that the current user can chat with.
     */
    public static function getAvailableUsers($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return collect();
        }
        
        $availableUsers = collect();

        if ($user->role === 'admin') {
            // Admin can chat with anyone
            $allUsers = User::where('id', '!=', $userId)->get();
            $availableUsers = $availableUsers->merge($allUsers);
        } elseif ($user->role === 'client') {
            // Client can chat with:
            // 1. Admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $availableUsers->push($admin);
            }

            // 2. Hired students (students with accepted applications for this client's jobs)
            $hiredStudents = User::where('role', 'student')
                ->whereHas('applications', function ($query) use ($user) {
                    $query->where('status', 'accepted')
                        ->whereHas('job', function ($q) use ($user) {
                            $q->where('client_id', $user->id);
                        });
                })
                ->get();
            
            $availableUsers = $availableUsers->merge($hiredStudents);
        } elseif ($user->role === 'student') {
            // Student can chat with:
            // 1. Admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $availableUsers->push($admin);
            }

            // 2. Clients they work for (clients who have accepted their applications)
            $clients = User::where('role', 'client')
                ->whereHas('jobs', function ($query) use ($user) {
                    $query->whereHas('applications', function ($q) use ($user) {
                        $q->where('student_id', $user->id)
                          ->where('status', 'accepted');
                    });
                })
                ->get();
            
            $availableUsers = $availableUsers->merge($clients);
        }

        return $availableUsers->unique('id');
    }

    /**
     * Get or create a conversation between two users.
     */
    public static function getOrCreateConversation($user1Id, $user2Id)
    {
        // Ensure user1_id is always smaller than user2_id for consistency
        $ids = [$user1Id, $user2Id];
        sort($ids);
        
        $conversation = Conversation::where('user1_id', $ids[0])
            ->where('user2_id', $ids[1])
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $ids[0],
                'user2_id' => $ids[1],
            ]);
        }

        return $conversation;
    }

    /**
     * Check if two users can chat with each other.
     */
    public static function canChat($user1Id, $user2Id)
    {
        $user1 = User::find($user1Id);
        $user2 = User::find($user2Id);

        if (!$user1 || !$user2) {
            return false;
        }

        // Admin can chat with anyone
        if ($user1->role === 'admin' || $user2->role === 'admin') {
            return true;
        }

        // Client and Student can chat if student is hired by client
        if ($user1->role === 'client' && $user2->role === 'student') {
            return Application::where('student_id', $user2->id)
                ->where('status', 'accepted')
                ->whereHas('job', function ($query) use ($user1) {
                    $query->where('client_id', $user1->id);
                })
                ->exists();
        }

        if ($user1->role === 'student' && $user2->role === 'client') {
            return Application::where('student_id', $user1->id)
                ->where('status', 'accepted')
                ->whereHas('job', function ($query) use ($user2) {
                    $query->where('client_id', $user2->id);
                })
                ->exists();
        }

        return false;
    }
}

