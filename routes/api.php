<?php

use App\Http\Controllers\Api\AdminController as ApiAdminController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\ChatController as ApiChatController;
use App\Http\Controllers\Api\ClientController as ApiClientController;
use App\Http\Controllers\Api\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\StudentController as ApiStudentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    
    // Student routes
    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/dashboard', [ApiStudentController::class, 'dashboard']);
        Route::get('/profile', [ApiStudentController::class, 'profile']);
        Route::post('/profile', [ApiStudentController::class, 'updateProfile']);
        Route::get('/ratings', [ApiStudentController::class, 'ratings']);
        Route::get('/jobs', [ApiStudentController::class, 'browseJobs']);
        Route::get('/jobs/{job}', [ApiStudentController::class, 'showJob']);
        Route::post('/jobs/{job}/apply', [ApiStudentController::class, 'storeApplication']);
        Route::get('/applications', [ApiStudentController::class, 'myApplications']);
        Route::post('/applications/{application}/withdraw', [ApiStudentController::class, 'withdrawApplication']);
        Route::post('/applications/{application}/complete', [ApiStudentController::class, 'submitCompletion']);
        Route::get('/invoices', [ApiStudentController::class, 'invoices']);
        Route::get('/invoices/{invoice}', [ApiStudentController::class, 'showInvoice']);
        Route::get('/invoices/{invoice}/download', [ApiStudentController::class, 'downloadInvoice']);
    });

    // Notifications (available to all authenticated users)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [ApiNotificationController::class, 'index']);
        Route::get('/unread-count', [ApiNotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [ApiNotificationController::class, 'markAsRead']);
        Route::post('/read-all', [ApiNotificationController::class, 'markAllAsRead']);
    });

    // Chat routes (available to all authenticated users)
    Route::prefix('chat')->group(function () {
        Route::get('/conversations', [ApiChatController::class, 'conversations']);
        Route::get('/available-users', [ApiChatController::class, 'availableUsers']);
        Route::get('/unread-count', [ApiChatController::class, 'unreadCount']);
        Route::post('/conversations', [ApiChatController::class, 'createConversation']);
        Route::get('/conversations/{conversation}/messages', [ApiChatController::class, 'messages']);
        Route::post('/conversations/{conversation}/messages', [ApiChatController::class, 'sendMessage']);
    });
    
    // Client routes
    Route::prefix('client')->middleware('role:client')->group(function () {
        Route::get('/dashboard', [ApiClientController::class, 'dashboard']);
        Route::get('/jobs', [ApiClientController::class, 'index']);
        Route::post('/jobs', [ApiClientController::class, 'store']);
        Route::get('/jobs/{job}', [ApiClientController::class, 'show']);
        Route::put('/jobs/{job}', [ApiClientController::class, 'update']);
        Route::delete('/jobs/{job}', [ApiClientController::class, 'destroy']);
        Route::post('/applications/{application}/accept', [ApiClientController::class, 'acceptApplication']);
        Route::post('/applications/{application}/reject', [ApiClientController::class, 'rejectApplication']);
        Route::post('/applications/{application}/rate', [ApiClientController::class, 'rateStudent']);
        Route::get('/applications/{application}/rating', [ApiClientController::class, 'getRating']);
        Route::get('/invoices', [ApiClientController::class, 'invoices']);
        Route::get('/invoices/{invoice}', [ApiClientController::class, 'showInvoice']);
        Route::get('/invoices/{invoice}/download', [ApiClientController::class, 'downloadInvoice']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/verifications/pending', [ApiAdminController::class, 'pendingVerifications']);
        Route::get('/students', [ApiAdminController::class, 'allStudents']);
        Route::get('/clients', [ApiAdminController::class, 'allClients']);
        Route::get('/verifications/stats', [ApiAdminController::class, 'verificationStats']);
        Route::post('/students/{user}/approve', [ApiAdminController::class, 'approveStudent']);
        Route::post('/students/{user}/reject', [ApiAdminController::class, 'rejectStudent']);
        Route::post('/users/{user}/ban', [ApiAdminController::class, 'banUser']);
        Route::post('/users/{user}/unban', [ApiAdminController::class, 'unbanUser']);
        Route::delete('/users/{user}', [ApiAdminController::class, 'deleteUser']);
        Route::get('/completions/pending', [ApiAdminController::class, 'pendingCompletions']);
        Route::get('/completions', [ApiAdminController::class, 'allCompletions']);
        Route::post('/completions/{completion}/approve', [ApiAdminController::class, 'approveCompletion']);
        Route::post('/completions/{completion}/reject', [ApiAdminController::class, 'rejectCompletion']);
        Route::get('/jobs', [ApiAdminController::class, 'allJobs']);
        Route::get('/invoices', [ApiAdminController::class, 'allInvoices']);
        Route::post('/invoices/{invoice}/mark-paid', [ApiAdminController::class, 'markInvoicePaid']);
    });
});

