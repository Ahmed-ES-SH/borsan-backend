<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleInteractionsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FooterLinkController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\TermsConditionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// ------------------------------
// start public Routes ------------
// ------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Route::post('/send-verfiy-email', [UserController::class, 'sendVerfiyEmail']);
Route::get('/verify-email/{id}', [UserController::class, 'verfiyEmail']);




// ----------------------------------------
//  main Categories Routes ----------------
// ----------------------------------------

Route::get('/public-categories', [CategoryController::class, 'publicCategories']);
Route::get('/categories', [CategoryController::class, 'index']);



// ----------------------------------------
//  Articles Categories Routes ------------
// ----------------------------------------

Route::get('/public-article-categories', [ArticleCategoryController::class, 'publicCategories']);
Route::get('/article-categories', [ArticleCategoryController::class, 'index']);




// ------------------------------------
//  Articles  Routes ------------
// ------------------------------------

Route::get('/top-ten-articles', [ArticleController::class, 'topTenArticlesByViews']);
Route::get('/articles-by-search', [ArticleController::class, 'getPublishedArticlesBySearch']);


// ------------------------------------
//  Privacy Policy  Routes ------------
// ------------------------------------

Route::get('/users-points', [PrivacyPolicyController::class, 'index']);



// --------------------------------
//  TermsCondition Routes ------
// --------------------------------


Route::get('/users-terms', [TermsConditionController::class, 'index']);


// -------------------------
//  About Detailes Routes --
// -------------------------

Route::get('/detailes', [AboutController::class, 'index']);




// -------------------------
//  Auth Routes ------------
// -------------------------

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);



// -------------------------
//  google Routes ----------
// -------------------------

Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);



// ---------------------------------
// Contact  Messages Routes --------
// ---------------------------------

Route::post('/add-contact-message', [ContactMessageController::class, 'store']);

// ----------------------------------------
//  Questions Answers Routes --------------
// ----------------------------------------

Route::get('/approvedQuestions', [QuestionAnswerController::class, 'approvedQuestions']);



// ----------------------------------------
// Subscribe to the newsletter ------------
// ----------------------------------------

Route::post('/subscribe', [MemberController::class, 'subscribe']);


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// ------------------------------
// End public Routes ------------
// ------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// -----------------------------------------
//  Start Protected Auth Routes ------------
// -----------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


// Route::middleware(['auth:sanctum'])->group(function () {

// -------------------------
//  Currentuser Routes -----
// -------------------------

Route::controller(AuthController::class)->group(function () {
    Route::get('/currentuser', 'getCurrentUser');
    Route::post('/logout', 'logout');
});





// -------------------------
//  Auth  users Routes -----
// -------------------------

Route::controller(UserController::class)->group(function () {
    Route::get('/user/{id}', 'show');
    Route::post('/update-user/{id}', 'update');
    Route::post('/check-password-user/{id}', 'checkPassword');
});



// -----------------------------
//  Notifications   Routes -----
// -----------------------------

Route::controller(NotificationController::class)->group(function () {
    Route::post('/send-notification', 'sendNotification');
    Route::post('/send-multiple-notification', 'sendMultipleNotification');
    Route::get('/notifications/{id}', 'getNotificationsForUser');
    Route::get('/last-ten-notifications', 'getLastTenNotifications');
    Route::post('/make-notifications-readed', 'makeAllNotificationsAsRead');
});


// ------------------------------------------
//  Article Interactions Routes -------------
// ------------------------------------------

Route::post('/add-article-interaction', [ArticleInteractionsController::class, 'addInterAction']);
Route::post('/update-article-interaction', [ArticleInteractionsController::class, 'updateInteraction']);
Route::delete('/cancle-article-interaction', [ArticleInteractionsController::class, 'removeInteraction']);


// ------------------------------------------
//  Article Comments Routes -----------------
// ------------------------------------------

Route::controller(ArticleCommentController::class)->group(function () {
    Route::post('/add-comment', 'store');
    Route::post('/update-comment/{id}', 'updateComment');
    Route::post('/like-comment/{id}', 'likeComment');
    Route::post('/unlike-comment/{id}', 'unlikeComment');
});


// ------------------------------------------
//  Conversations  Routes -------------------
// ------------------------------------------

Route::controller(ConversationController::class)->group(function () {
    // ✅ إنشاء محادثة جديدة
    Route::post('/conversations',  'store')->name('conversations.store');

    // ✅ جلب محادثة معينة مع الرسائل
    Route::get('/conversations/{id}',  'getConversation')->name('conversations.get');

    // ✅ جلب جميع المحادثات الخاصة بالمستخدم
    Route::get('/user/{id}/conversations',  'getUserConversations')->name('conversations.user');

    // ✅ حذف رسالة معينة
    Route::delete('/messages/{messageId}',  'deleteMessage')->name('messages.delete');

    // ✅ تحديد الرسائل كمقروءة
    Route::post('/conversations/{conversationId}/mark-as-read/{userId}',  'markAsRead')->name('messages.markAsRead');

    // ✅ حظر مستخدم في محادثة
    Route::post('/conversations/{conversationId}/block',  'blockUser')->name('conversations.block');

    // ✅ إلغاء الحظر عن مستخدم في محادثة
    Route::delete('/conversations/{conversationId}/unblock',  'unblockUser')->name('conversations.unblock');
});
// });


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// -----------------------------------------
//  End Protected Auth Routes ------------
// -----------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------






//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// -------------------------------
// start  Admin  Routes ----------
// -------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


// Route::middleware(['auth:sanctum', 'checkAdmin'])->group(function () {

// -------------------------
// users Routes ------------
// -------------------------

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::get('/users-ids', 'getUsersIds');
    Route::get('/users-count', 'getUsersCount');
    Route::post('/search-for-user-by-name', 'searchForUsersByName');
    Route::delete('/delete-user/{id}', 'destroy');
});



// ---------------------------------------
// main Categories Routes ------------
// ---------------------------------------

Route::controller(CategoryController::class)->group(function () {
    Route::post('/add-category', 'store');
    Route::get('/category/{id}', 'show');
    Route::post('/update-category/{id}', 'update');
    Route::post('/delete-category/{id}', 'destroy');
});


// ---------------------------------------
// Articles Categories Routes ------------
// ---------------------------------------

Route::controller(ArticleCategoryController::class)->group(function () {
    Route::post('/add-article-category', 'store');
    Route::get('/article-category/{id}', 'show');
    Route::post('/update-article-category/{id}', 'update');
    Route::delete('/delete-article-category/{id}', 'destroy');
});


// ---------------------------------------
// Articles  Routes ----------------------
// ---------------------------------------

Route::controller(ArticleController::class)->group(function () {
    Route::get('/articles', 'index');
    Route::get('/articles-by-status/{status}', 'getArticlesByStatus');
    Route::post('/get-articles-by-search', 'getArticlesBySearch');
    Route::post('/add-article', 'store');
    Route::get('/article/{id}', 'show');
    Route::post('/update-article/{id}', 'update');
    Route::post('/delete-article/{id}', 'destroy');
});




// ---------------------------------
// Contact  Messages Routes --------
// ---------------------------------

Route::controller(ContactMessageController::class)->group(function () {
    Route::get('/contact-messages', 'index');
    Route::get('/contact-message/{id}', 'show');
    Route::post('/update-contact-message/{id}', 'update');
    Route::delete('/contact-message/{id}', 'destroy');
});


// ---------------------------------
// Questions Answers Routes --------
// ---------------------------------

Route::controller(QuestionAnswerController::class)->group(function () {
    Route::get('/all-faqs', 'index');
    Route::post('/add-faq', 'store');
    Route::get('/get-faq/{id}', 'show');
    Route::post('/update-faq/{id}', 'update');
    Route::delete('/delete-faq/{id}', 'destroy');
});

// ---------------------------------
// Footer  Links Routes ------------
// ---------------------------------


Route::controller(FooterLinkController::class)->group(function () {
    Route::get('/all-lists', 'getLinksByList');
    Route::post('/add-link', 'store');
    Route::get('/get-link/{id}', 'show');
    Route::post('/update-link/{id}', 'update');
    Route::delete('/delete-link/{id}', 'destroy');
});


// ---------------------------------
// privacy policy Routes -----------
// ---------------------------------


Route::controller(PrivacyPolicyController::class)->group(function () {
    Route::post('/add-user-point', 'store');
    Route::post('/update-user-point/{id}', 'update');
    Route::delete('/user-point/{id}', 'destroy');
});



// --------------------------------
//  TermsCondition Routes ------
// --------------------------------

Route::controller(TermsConditionController::class)->group(function () {
    Route::post('/add-user-term', 'store');
    Route::post('/update-user-term/{id}', 'update');
    Route::delete('/user-term/{id}', 'destroy');
});




// --------------------------------
//  Social Contant Info Routes ----
// --------------------------------


Route::controller(SocialAccountController::class)->group(function () {
    Route::get('/social-contact-info', 'getAccounts');
    Route::post('/update-social-contact-info', 'update');
});




// -------------------------
//  News Letter Routes -----
// -------------------------

Route::controller(MemberController::class)->group(function () {
    Route::get('/members',  'index');
    Route::post('/members-by-email/{searchcontent}',  'getMembersByEmail');
    Route::get('/get-members-ids',  'getMembersIds');
    Route::post('/send-newsletter',  'sendNewsletter');
    Route::delete('/unsubscribe/{id}',  'unsubscribe');
});



// -------------------------
//  About Detailes Routes --
// -------------------------

Route::post('/update-detailes', [AboutController::class, 'update']);



// });




//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// -------------------------------
// End  Admin  Routes ----------
// -------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
