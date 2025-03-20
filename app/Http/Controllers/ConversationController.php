<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Conversation;
use App\Models\ConversationBlock;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    use ApiResponse;
    protected $chatService;
    protected $imageservice;

    public function __construct(ChatService $chatService, ImageService $imageService)
    {
        $this->chatService = $chatService;
        $this->imageservice = $imageService;
    }


    public function StoreConversation(Request $request)
    {
        try {
            // التحقق من صحة البيانات المدخلة
            $request->validate([
                'participant_one_id' => 'required|exists:users,id',
                'participant_two_id' => 'required|exists:users,id',
            ]);

            // التأكد من أن المستخدمين مختلفون
            if ($request->participant_one_id === $request->participant_two_id) {
                return $this->errorResponse('Participants must be different users.', 400);
            }

            // التحقق مما إذا كانت المحادثة موجودة مسبقًا
            $conversation = Conversation::where(function ($query) use ($request) {
                $query->where('participant_one_id', $request->participant_one_id)
                    ->where('participant_two_id', $request->participant_two_id);
            })->orWhere(function ($query) use ($request) {
                $query->where('participant_one_id', $request->participant_two_id)
                    ->where('participant_two_id', $request->participant_one_id);
            })->with('messages')->first();

            // إذا كانت المحادثة موجودة بالفعل، نعيدها
            if ($conversation) {
                return $this->successResponse($conversation, 201, 'Conversation already exists.');
            }
            // إنشاء محادثة جديدة
            $conversation = Conversation::create([
                'participant_one_id' => $request->participant_one_id,
                'participant_two_id' => $request->participant_two_id,
            ]);


            return $this->successResponse($conversation, 201, 'Conversation created successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getConversation($id)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('User not authenticated', 401);
            }

            // جلب المحادثة مع الرسائل والتحقق من وجود الحظر
            $conversation = Conversation::withExists(['block'])
                ->with([
                    'messages' => function ($query) {
                        $query->orderBy('created_at', 'desc')->paginate(20);
                    },
                    'block' // جلب بيانات الحظر إن وجدت
                ])
                ->where(function ($query) use ($userId) {
                    $query->where('participant_one_id', $userId)
                        ->orWhere('participant_two_id', $userId);
                })
                ->findOrFail($id);

            // تحديد المستخدم الآخر
            $otherUserId = ($conversation->participant_one_id == $userId)
                ? $conversation->participant_two_id
                : $conversation->participant_one_id;

            // جلب بيانات المستخدمين
            $currentUser = User::select(['id', 'name', 'image'])->find($userId);
            $otherUser = User::select(['id', 'name', 'image'])->find($otherUserId);

            // التحقق من حالة الحظر
            $blockData = null;
            if ($conversation->block) {
                $blockData = [
                    'blocked_by' => $conversation->block->blocked_by,
                    'blocked_user' => $conversation->block->blocked_user,
                ];
            }

            // تجهيز الاستجابة
            return $this->successResponse([
                'id' => $conversation->id,
                'participants' => [
                    'current_user' => [
                        'id' => $currentUser->id,
                        'name' => $currentUser->name,
                        'image' => $currentUser->image,
                    ],
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'image' => $otherUser->image,
                    ],
                ],
                'messages' => $conversation->messages,
                'block_info' => $blockData, // معلومات الحظر إن وجدت
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function sendMessage(StoreMessageRequest $request)
    {
        $data = $request->validated();

        $response = $this->chatService->sendMessage($data);

        if (!$response['success']) {
            return response()->json(['error' => $response['error']], 500);
        }

        return response()->json(['message' => 'Message sent successfully', 'data' => $response['message']], 201);
    }


    public function getUserConversations($id)
    {
        try {
            $conversations = Conversation::with(['participantOne:id,name,image', 'participantTwo:id,name,image'])
                ->where('participant_one_id', $id)
                ->orWhere('participant_two_id', $id)
                ->orderByDesc('updated_at')
                ->paginate(20);
            return $this->paginationResponse($conversations, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function deleteMessage($messageId)
    {
        try {
            $message = Message::findOrFail($messageId);

            if (isset($message->attachment)) {
                $this->imageservice->deleteChatAttachment($message);
            }

            $message->delete();

            return response()->json(['message' => 'Message deleted successfully', 200]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function markAsRead($conversationId, $userId)
    {
        try {
            // البحث عن جميع الرسائل غير المقروءة من الطرف الآخر
            $messages = Message::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', $userId)
                ->where('is_read', false);

            // التحقق مما إذا كانت هناك رسائل غير مقروءة
            if ($messages->count() === 0) {
                return response()->json([
                    'message' => "No unread messages found."
                ], 200);
            }

            // تحديث جميع الرسائل دفعة واحدة
            $messages->update(['is_read' => true]);

            return response()->json([
                'message' => 'All unread messages marked as read.'
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function blockUser(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('User not authenticated', 401);
            }

            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'blocked_user' => 'required|exists:users,id',
            ]);

            $conversationId = $request->conversation_id;
            $blockedUserId = $request->blocked_user;

            // التحقق من أن المستخدم هو أحد المشاركين في المحادثة
            $conversation = Conversation::where('id', $conversationId)
                ->where(function ($query) use ($userId) {
                    $query->where('participant_one_id', $userId)
                        ->orWhere('participant_two_id', $userId);
                })->first();

            if (!$conversation) {
                return $this->errorResponse('Conversation not found or unauthorized', 403);
            }

            // التحقق من وجود حظر سابق
            $existingBlock = ConversationBlock::where('conversation_id', $conversationId)
                ->where('blocked_by', $userId)
                ->where('blocked_user', $blockedUserId)
                ->first();

            if ($existingBlock) {
                return $this->errorResponse('User is already blocked', 400);
            }

            // إضافة الحظر إلى قاعدة البيانات
            ConversationBlock::create([
                'conversation_id' => $conversationId,
                'blocked_by' => $userId,
                'blocked_user' => $blockedUserId,
            ]);

            return $this->successResponse('User has been blocked successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function unblockUser(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('User not authenticated', 401);
            }

            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'blocked_user' => 'required|exists:users,id',
            ]);

            $conversationId = $request->conversation_id;
            $blockedUserId = $request->blocked_user;

            // التحقق من وجود سجل الحظر
            $block = ConversationBlock::where('conversation_id', $conversationId)
                ->where('blocked_by', $userId)
                ->where('blocked_user', $blockedUserId)
                ->first();

            if (!$block) {
                return $this->errorResponse('No block record found', 404);
            }

            // حذف سجل الحظر
            $block->delete();

            return $this->successResponse('User has been unblocked successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
