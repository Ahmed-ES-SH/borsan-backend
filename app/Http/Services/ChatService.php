<?php

namespace App\Services;

use App\Http\Services\ImageService;
use App\Models\Message;
use Illuminate\Support\Arr;
use Pusher\Pusher;

class ChatService
{
    protected $pusher;
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => config('broadcasting.connections.pusher.options.useTLS'),
            ]
        );

        $this->imageService = $imageService;
    }

    /**
     * إرسال رسالة في المحادثة عبر Pusher
     */
    public function sendMessage(array $data)
    {
        try {
            // ✅ حفظ الرسالة في قاعدة البيانات
            $message = Message::create(Arr::except($data, ['attachment']));

            // ✅ معالجة المرفقات إن وجدت
            if (isset($data['attachment'])) {
                $this->imageService->uploadChatAttachment($data['attachment'], $message);
            }

            // ✅ إرسال الرسالة عبر Pusher
            $this->pusher->trigger(
                'conversation.' . $data['conversation_id'], // قناة المحادثة
                'MessageSent', // اسم الحدث
                [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message,
                    'attachment' => $message->attachment ?? null,
                    'created_at' => $message->created_at->toDateTimeString(),
                ]
            );

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
