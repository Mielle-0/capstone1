<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiSettingsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackPortalController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\ReportController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Public Feedback Portal
Route::get('/', [FeedbackPortalController::class, 'showForm'])
    ->name('feedback.form');

Route::post('/feedback/send-code', [FeedbackPortalController::class, 'sendCode'])
    ->name('feedback.sendCode');

Route::post('/feedback/submit', [FeedbackPortalController::class, 'submitFeedback'])
    ->middleware('throttle:3,1') // Only 3 submissions per minute per IP
    ->name('feedback.submit');

Route::get('/my-feedback/view/{email}', [FeedbackPortalController::class, 'guestView'])
    ->name('feedback.guest.view')
    ->middleware('signed');

Route::get('/my-feedback/timeline/{email}/{id}', [FeedbackPortalController::class, 'guestTimeline'])
    ->name('feedback.guest.timeline')
    ->middleware('signed');
    
Route::post('/my-feedback/reply/{id}', [FeedbackPortalController::class, 'guestReply'])
    ->name('feedback.guest.reply');

Route::post('/my-feedback/rate/{id}', [FeedbackPortalController::class, 'guestRate'])
    ->name('feedback.guest.rate');

Route::post('/request-feedback-history', [FeedbackPortalController::class, 'sendHistoryLink'])
    ->name('feedback.requestHistory');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login.form');
    
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1') // 5 attempts per minute
        ->name('login');
});



Route::middleware(['web', 'auth'])->group(function () {

    // All Roles
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::put('/settings/update', [UserController::class, 'updateSettings'])->name('settings.update');


    Route::get('/encode', [WorkflowController::class, 'encodeIndex'])->name('workflow.encode');
    Route::post('/store', [WorkflowController::class, 'storeManual'])->name('workflow.store');

    // 2. Validation
    Route::get('/validation', [WorkflowController::class, 'validationIndex'])->name('workflow.validation');
    Route::post('/validate/{id}', [WorkflowController::class, 'processValidation'])->name('workflow.process');
    Route::get('/validation/details/{id}', [WorkflowController::class, 'validationDetails'])->name('workflow.feedback_details');
    Route::get('/autocomplete/departments', [WorkflowController::class, 'autocompleteDepartments'])->name('departments.autocomplete');

    // 3. Action
    Route::get('/action', [WorkflowController::class, 'actionIndex'])->name('workflow.action');
    Route::get('/department/{dep_id}', [WorkflowController::class, 'actionIndex'])->name('workflow.department_action');
    Route::post('/submit-action/{id}', [WorkflowController::class, 'submitAction'])->name('workflow.submit_action');
    Route::post('/ticket/{ticket}/reject', [WorkflowController::class, 'dropTicket'])
    ->name('workflow.reject_ticket');
    // Audit Trail / View Ticket
    Route::get('/ticket/{uuid}', [WorkflowController::class, 'showTicket'])->name('workflow.show_ticket');

    // 4. Verification
    Route::get('/verification', [WorkflowController::class, 'verificationIndex'])->name('workflow.verification');
    Route::post('/verify/{id}', [WorkflowController::class, 'verifyFinal'])->name('workflow.verify');


    Route::middleware('role:Encoder')->group(function () {
        Route::get('/encode-feedback', [FeedbackController::class, 'create']);
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
        Route::get('/analysis', [ReportController::class, 'analysis'])->name('analysis');
        Route::get('/satisfaction', [ReportController::class, 'satisfaction'])->name('satisfaction');
    });



    Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store');

    // Validator
    Route::get('/for-validation', [FeedbackController::class, 'forValidation'])->name('feedback.validation');
    Route::post('/feedback/validate/{id}', [FeedbackController::class, 'validateFeedback'])->name('feedback.validate');

    // Display TImeline of Ticket
    Route::get('/timeline/{id}', [WorkflowController::class, 'showTimeline'])->name('workflow.timeline');

});


// Admin specific routes (Auth is implied/checked by 'role')
Route::middleware('role:Super Admin')->prefix('admin')->group(function () {

    Route::get('/audit-log', [AdminController::class, 'auditLog']);
    Route::get('/reports/export', [AdminController::class, 'exportReport'])
        ->name('admin.reports.export');
    
    // Manage Departments Page
    Route::get('/manage-departments', [AdminController::class, 'manage_departments'])->name('admin.departments.index');
    Route::post('/manage-departments/assign', [AdminController::class, 'assignUser'])->name('admin.departments.assign');
    Route::put('/manage-departments/{id}', [AdminController::class, 'updateDepartment']);

    
    // User Management
    Route::get('/manage-users', [AdminController::class, 'manage_users'])->name('admin.users.index');
    Route::post('/manage-users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('/manage-users/{id}', [AdminController::class, 'update'])->name('admin.users.update');

    Route::get('/resolved-tickets', [AdminController::class, 'resolvedTickets'])->name('admin.resolved_tickets');
    Route::get('/generate-code', [AdminController::class, 'generateUniqueCode'])->name('admin.users.generate-code');


    // AI Management
    Route::get('/ai-settings', [AiSettingsController::class, 'index'])->name('ai');
    Route::post('/ai-settings', [AiSettingsController::class, 'update'])->name('admin.settings.ai.update');

});


Route::middleware('signed')->group(function () {
    Route::get('/setup-password/{user}', [LoginController::class, 'create'])
        ->name('password.setup');
        
    Route::post('/setup-password/{user}', [LoginController::class, 'store'])
        ->name('password.setup.store');
});

// Password Reset Routes
Route::get('/forgot-password', [LoginController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.send-email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');
