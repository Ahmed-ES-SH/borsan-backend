<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNotificationRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Services\NotificationService;
use App\Models\Notification;

class NotificationController extends Controller
{
    use ApiResponse;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function sendNotification(SendNotificationRequest $request)
    {
        $data = $request->validated();
        $result = $this->notificationService->sendNotification($data);

        if (!$result['success']) {
            return $this->errorResponse([
                "ar" => "فشل إرسال الإشعار. السبب: " . $result['message'],
                "en" => "Notification sending failed. Reason: " . $result['message']
            ], 500);
        }

        return $this->successResponse($result['notification'], 200);
    }


    public function sendNotifications(SendNotificationRequest $request)
    {
        $data = $request->validated(); // التحقق من صحة البيانات
        $response = $this->notificationService->SendMultipleNotifications($data); // استدعاء الدالة من الخدمة

        if ($response['success']) {
            return response()->json(['message' => 'Notifications sent successfully.'], 200);
        } else {
            return response()->json(['error' => $response['message']], 500);
        }
    }


    public function getNotificationsForUser($id)
    {
        try {
            $notifications = Notification::where('user_id', $id)
                ->orderDescBy('created_at')
                ->with('sender')
                ->paginate(30);

            if ($notifications->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($notifications, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function makeAllNotificationsAsRead($id)
    {
        try {
            // تحديث جميع الإشعارات غير المقروءة دفعة واحدة
            $updatedCount = Notification::where('user_id', $id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            if ($updatedCount === 0) {
                return $this->errorResponse([
                    "ar" => "لا توجد إشعارات غير مقروءة.",
                    "en" => "No unread notifications found."
                ], 404);
            }

            return $this->successResponse([
                "ar" => "تم تحديث جميع الإشعارات إلى مقروءة.",
                "en" => "All notifications have been marked as read."
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getLastTenNotifications($id)
    {
        try {
            $notifications = Notification::where('user_id', $id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            if ($notifications->isEmpty()) {
                return $this->noContentResponse();
            }


            return $this->successResponse($notifications, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
