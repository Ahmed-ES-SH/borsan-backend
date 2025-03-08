<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\ArticleInteractions;
use App\Models\UserArticleInteraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleInteractionsController extends Controller
{
    use ApiResponse;


    public function addInterAction(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'interaction_type' => 'required|in:love,like,dislike,laughter', // تصحيح الخطأ الإملائي
                'user_id' => 'required|exists:users,id',
                'article_id' => 'required|exists:articles,id',
            ]);

            if ($validation->fails()) {
                return $this->errorResponse($validation->errors(), 422);
            }

            // التحقق مما إذا كان المستخدم قد تفاعل مسبقًا مع نفس المقال
            $existsInteraction = UserArticleInteraction::where('user_id', $request->user_id)
                ->where('article_id', $request->article_id)
                ->exists(); // تحسين البحث باستخدام exists()

            if ($existsInteraction) {
                return $this->errorResponse("User has already reacted to this article.", 400);
            }

            $interactionType = $request->interaction_type;

            // البحث عن التفاعل أو إنشاؤه إن لم يكن موجودًا
            $interaction = ArticleInteractions::firstOrCreate(
                ['article_id' => $request->article_id],
                ['loves' => 0, 'likes' => 0, 'dislikes' => 0, 'laughters' => 0] // تصحيح الاسم
            );

            // تحديث العدّاد الخاص بالتفاعل المناسب
            $interaction->increment(match ($interactionType) {
                'love' => 'loves',
                'like' => 'likes',
                'dislike' => 'dislikes',
                'laughter' => 'laughters'
            });

            // تخزين التفاعل في جدول المستخدمين
            UserArticleInteraction::create([
                'user_id' => $request->user_id,
                'article_id' => $request->article_id,
                'interaction_type' => $interactionType
            ]);

            return $this->successResponse($interaction, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function updateInteraction(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'interaction_type' => 'required|in:love,like,dislike,laughter', // تصحيح التفاعل الجديد
                'user_id' => 'required|exists:users,id',
                'article_id' => 'required|exists:articles,id',
            ]);

            if ($validation->fails()) {
                return $this->errorResponse($validation->errors(), 422);
            }

            // البحث عن التفاعل الحالي للمستخدم مع المقال
            $userInteraction = UserArticleInteraction::where('user_id', $request->user_id)
                ->where('article_id', $request->article_id)
                ->first();

            if (!$userInteraction) {
                return $this->errorResponse("User has not reacted to this article yet.", 404);
            }

            $oldInteractionType = $userInteraction->interaction_type;
            $newInteractionType = $request->interaction_type;

            // إذا لم يغير المستخدم نوع التفاعل، لا داعي للتحديث
            if ($oldInteractionType === $newInteractionType) {
                return $this->errorResponse("No changes detected in the interaction type.", 400);
            }

            // تحديث عدد التفاعلات في جدول `ArticleInteractions`
            $interaction = ArticleInteractions::where('article_id', $request->article_id)->first();

            if (!$interaction) {
                return $this->errorResponse("Article interaction record not found.", 404);
            }

            // تقليل العدد من التفاعل السابق
            match ($oldInteractionType) {
                'love' => $interaction->decrement('loves'),
                'like' => $interaction->decrement('likes'),
                'dislike' => $interaction->decrement('dislikes'),
                'laughter' => $interaction->decrement('laughters'),
            };

            // زيادة العدد في التفاعل الجديد
            match ($newInteractionType) {
                'love' => $interaction->increment('loves'),
                'like' => $interaction->increment('likes'),
                'dislike' => $interaction->increment('dislikes'),
                'laughter' => $interaction->increment('laughters'),
            };

            // تحديث التفاعل في سجل المستخدم
            $userInteraction->update([
                'interaction_type' => $newInteractionType
            ]);

            return $this->successResponse($interaction, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function removeInteraction(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'article_id' => 'required|exists:articles,id',
            ]);

            if ($validation->fails()) {
                return $this->errorResponse($validation->errors(), 422);
            }

            // البحث عن التفاعل الحالي للمستخدم مع المقال
            $userInteraction = UserArticleInteraction::where('user_id', $request->user_id)
                ->where('article_id', $request->article_id)
                ->first();

            if (!$userInteraction) {
                return $this->errorResponse("User has not reacted to this article.", 404);
            }

            $interactionType = $userInteraction->interaction_type;

            // البحث عن سجل التفاعلات في `ArticleInteractions`
            $interaction = ArticleInteractions::where('article_id', $request->article_id)->first();

            if (!$interaction) {
                return $this->errorResponse("Article interaction record not found.", 404);
            }

            // تقليل العدد من نوع التفاعل الحالي
            match ($interactionType) {
                'love' => $interaction->decrement('loves'),
                'like' => $interaction->decrement('likes'),
                'dislike' => $interaction->decrement('dislikes'),
                'laughter' => $interaction->decrement('laughters'),
            };

            // حذف التفاعل من جدول المستخدمين
            $userInteraction->delete();

            return $this->successResponse("Interaction removed successfully.", 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
