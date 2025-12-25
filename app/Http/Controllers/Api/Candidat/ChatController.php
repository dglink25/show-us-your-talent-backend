<?php

namespace App\Http\Controllers\Api\Candidat;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\ChatNotification;
use App\Models\Category;
use App\Models\Edition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatController extends Controller
{
    /**
     * Get or create chat room for a category
     */
    public function getOrCreateRoom(Request $request, $categoryId) {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $edition = Edition::where('statut', 'active')->latest()->first();

            if (!$edition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune édition active trouvée'
                ], 404);
            }

            // Vérifier si la catégorie existe
            $category = Category::find($categoryId);
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catégorie non trouvée'
                ], 404);
            }

            // Vérifier si l'utilisateur est un candidat de cette catégorie
            if ($user->type_compte === 'candidat') {
                $isCandidatInCategory = $user->candidatures()
                    ->where('categorie_id', $categoryId)
                    ->where('edition_id', $edition->id)
                    ->where('statut', 'validée')
                    ->exists();

                if (!$isCandidatInCategory) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous n\'êtes pas candidat dans cette catégorie'
                    ], 403);
                }
            }

            // Chercher ou créer la salle de chat
            $room = ChatRoom::firstOrCreate(
                [
                    'category_id' => $categoryId,
                    'edition_id' => $edition->id
                ],
                [
                    'name' => 'Chat - ' . $category->nom,
                    'description' => 'Discussion pour les candidats de cette catégorie',
                    'status' => 'active'
                ]
            );

            // Ajouter l'utilisateur comme participant s'il ne l'est pas déjà
            $participant = ChatParticipant::firstOrCreate(
                [
                    'chat_room_id' => $room->id,
                    'user_id' => $user->id
                ],
                [
                    'role' => $user->type_compte === 'promoteur' ? 'promoteur' : 'candidat',
                    'last_seen' => now()
                ]
            );

            // Charger les données nécessaires
            $room->load([
                'category',
                'participants.user' => function($query) {
                    $query->select('id', 'nom', 'prenoms', 'photo_url', 'type_compte');
                }
            ]);

            // Compter les messages non lus
            $unreadCount = ChatMessage::where('chat_room_id', $room->id)
                ->where('created_at', '>', $participant->last_seen)
                ->where('user_id', '!=', $user->id)
                ->count();

            return response()->json([
                'success' => true,
                'room' => $room,
                'unread_count' => $unreadCount,
                'participant' => $participant
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur création chat room: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, $roomId) {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
            'type' => 'sometimes|string|in:text,image,file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $room = ChatRoom::find($roomId);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salle de chat non trouvée'
                ], 404);
            }

            // Vérifier si l'utilisateur est participant
            $isParticipant = ChatParticipant::where('chat_room_id', $roomId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas participant de cette conversation'
                ], 403);
            }

            // Créer le message
            $message = ChatMessage::create([
                'chat_room_id' => $roomId,
                'user_id' => $user->id,
                'message' => $request->message,
                'type' => $request->type ?? 'text',
                'metadata' => $request->metadata ?? null
            ]);

            // Créer des notifications pour les autres participants
            $participants = ChatParticipant::where('chat_room_id', $roomId)
                ->where('user_id', '!=', $user->id)
                ->where('is_muted', false)
                ->get();

            foreach ($participants as $participant) {
                ChatNotification::create([
                    'user_id' => $participant->user_id,
                    'chat_room_id' => $roomId,
                    'chat_message_id' => $message->id,
                    'type' => $user->type_compte === 'promoteur' ? 'promoteur_message' : 'new_message',
                    'message' => $user->type_compte === 'promoteur' 
                        ? 'Le promoteur a envoyé un message'
                        : 'Nouveau message de ' . $user->prenoms,
                    'data' => json_encode([
                        'sender_id' => $user->id,
                        'sender_name' => $user->prenoms . ' ' . $user->nom,
                        'room_name' => $room->name,
                        'category_id' => $room->category_id,
                        'message_preview' => substr($request->message, 0, 50)
                    ]),
                    'is_read' => false
                ]);
            }

            // Charger les relations
            $message->load(['user' => function($query) {
                $query->select('id', 'nom', 'prenoms', 'photo_url', 'type_compte');
            }]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'room' => $room
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur envoi message: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get room messages
     */
    public function getMessages(Request $request, $roomId){
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $room = ChatRoom::find($roomId);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salle de chat non trouvée'
                ], 404);
            }

            // Vérifier la participation
            $isParticipant = ChatParticipant::where('chat_room_id', $roomId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $messages = ChatMessage::where('chat_room_id', $roomId)
                ->with(['user' => function($query) {
                    $query->select('id', 'nom', 'prenoms', 'photo_url', 'type_compte');
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            // Mettre à jour last_seen
            ChatParticipant::where('chat_room_id', $roomId)
                ->where('user_id', $user->id)
                ->update(['last_seen' => now()]);

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'room' => $room
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération messages: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get user's chat rooms
     */
    public function getUserRooms(Request $request){
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $edition = Edition::where('statut', 'active')->latest()->first();

            if (!$edition) {
                return response()->json([
                    'success' => true,
                    'rooms' => []
                ]);
            }

            $rooms = ChatRoom::where('edition_id', $edition->id)
                ->where('status', 'active')
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with([
                    'category',
                    'participants' => function($query) {
                        $query->with(['user' => function($q) {
                            $q->select('id', 'nom', 'prenoms', 'photo_url');
                        }]);
                    }
                ])
                ->get()
                ->map(function($room) use ($user) {
                    // Calculer les messages non lus
                    $participant = $room->participants()->where('user_id', $user->id)->first();
                    $unreadCount = 0;
                    
                    if ($participant && $participant->last_seen) {
                        $unreadCount = ChatMessage::where('chat_room_id', $room->id)
                            ->where('created_at', '>', $participant->last_seen)
                            ->where('user_id', '!=', $user->id)
                            ->count();
                    }
                    
                    // Dernier message
                    $lastMessage = ChatMessage::where('chat_room_id', $room->id)
                        ->with('user')
                        ->latest()
                        ->first();
                    
                    $room->unread_count = $unreadCount;
                    $room->last_message = $lastMessage;
                    return $room;
                });

            return response()->json([
                'success' => true,
                'rooms' => $rooms
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération rooms: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get notifications for user
     */
    public function getNotifications(Request $request) {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $notifications = ChatNotification::where('user_id', $user->id)
                ->with(['room.category', 'message.user'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            $unreadCount = ChatNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération notifications: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request, $notificationId) {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $notification = ChatNotification::where('user_id', $user->id)
                ->find($notificationId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }

            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'notification' => $notification
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur marquage notification: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(Request $request){
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            ChatNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications ont été marquées comme lues'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur marquage notifications: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get room participants
     */
    public function getParticipants(Request $request, $roomId){
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $room = ChatRoom::find($roomId);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salle de chat non trouvée'
                ], 404);
            }

            // Vérifier la participation
            $isParticipant = ChatParticipant::where('chat_room_id', $roomId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $participants = ChatParticipant::where('chat_room_id', $roomId)
                ->with(['user' => function($query) {
                    $query->select('id', 'nom', 'prenoms', 'photo_url', 'type_compte', 'universite');
                }])
                ->get()
                ->map(function($participant) use ($user) {
                    // Vérifier si l'utilisateur est en ligne (simplifié)
                    $participant->is_online = $participant->last_seen 
                        ? Carbon::parse($participant->last_seen)->diffInMinutes(now()) < 5
                        : false;
                    $participant->is_current_user = $participant->user_id === $user->id;
                    return $participant;
                });

            return response()->json([
                'success' => true,
                'participants' => $participants,
                'promoteur_count' => $participants->where('role', 'promoteur')->count(),
                'candidat_count' => $participants->where('role', 'candidat')->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération participants: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}