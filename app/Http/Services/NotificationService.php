<?php

namespace App\Http\Services;

use Pusher\Pusher;
use App\Models\Notification;

class NotificationService
{
    protected $pusher;

    public function __construct()
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
    }

    /**
     * إرسال إشعار للمستخدم عبر Pusher
     */
    public function sendNotification(array $data)
    {
        try {
            // حفظ الإشعار في قاعدة البيانات
            $notification = Notification::create($data);

            // إرسال الإشعار عبر Pusher
            $this->pusher->trigger(
                'notifications.' . $data['user_id'],
                'NotificationSent',
                [
                    'content' => $data['content'],
                    'user_id' => $data['user_id'],
                    'sender_id' => $data['sender_id'],
                    'is_read' => 0
                ]
            );



            return ['success' => true, 'notification' => $notification];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function SendMultipleNotifications(array $data)
    {
        try {
            $userIds = $data['user_ids']; // قائمة معرفات المستخدمين

            $notifications = [];
            foreach ($userIds as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'content' => $data['content'],
                    'sender_id' => $data['sender_id'],
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // حفظ جميع الإشعارات دفعة واحدة
            Notification::insert($notifications);

            // إرسال الإشعارات عبر Pusher لكل مستخدم
            foreach ($userIds as $userId) {
                $this->pusher->trigger(
                    'notifications.' . $userId,
                    'NotificationSent',
                    [
                        'content' => $data['content'],
                        'user_id' => $userId,
                        'sender_id' => $data['sender_id'],
                        'is_read' => 0
                    ]
                );
            }

            return ['success' => true, 'message' => 'Notifications sent successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
