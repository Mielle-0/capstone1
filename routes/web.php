<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiSettingsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackPortalController;
use App\Http\Controllers\WorkflowController;
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


    Route::get('/feedbacks', [UserController::class, 'feedbacks'])->name('feedbacks');
    Route::get('/search', [UserController::class, 'search'])->name('search');
    Route::get('/analytics', [UserController::class, 'analytics'])->name('analytics');
    Route::get('/tickets/create', [UserController::class, 'settings'])->name('settings');


    Route::get('/encode', [WorkflowController::class, 'encodeIndex'])->name('workflow.encode');
    Route::post('/store', [WorkflowController::class, 'storeManual'])->name('workflow.store');

    // 2. Validation
    Route::get('/validation', [WorkflowController::class, 'validationIndex'])->name('workflow.validation');
    Route::post('/validate/{id}', [WorkflowController::class, 'processValidation'])->name('workflow.process');
    Route::get('/validation/details/{id}', [WorkflowController::class, 'validationDetails'])->name('workflow.feedback_details');
    Route::get('/autocomplete/departments', [WorkflowController::class, 'autocompleteDepartments'])->name('departments.autocomplete');

    // 3. Action
    Route::get('/action', [WorkflowController::class, 'actionIndex'])->name('workflow.action');
    Route::post('/submit-action/{id}', [WorkflowController::class, 'submitAction'])->name('workflow.submit_action');
    
    // Audit Trail / View Ticket
    Route::get('/ticket/{uuid}', [WorkflowController::class, 'showTicket'])->name('workflow.show_ticket');

    // 4. Verification
    Route::get('/verification', [WorkflowController::class, 'verificationIndex'])->name('workflow.verification');
    Route::post('/verify/{id}', [WorkflowController::class, 'verifyFinal'])->name('workflow.verify');


    Route::middleware('role:Super Admin,Encoder')->group(function () {
        Route::get('/encode-feedback', [FeedbackController::class, 'create']);
    });



    Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store');

    // Validator
    Route::get('/for-validation', [FeedbackController::class, 'forValidation'])->name('feedback.validation');
    Route::post('/feedback/validate/{id}', [FeedbackController::class, 'validateFeedback'])->name('feedback.validate');

    // Put this in your protected/auth route group
    Route::get('/department/{dep_id}', [WorkflowController::class, 'departmentActionIndex'])->name('workflow.department_action');

});


// Admin specific routes (Auth is implied/checked by 'role')
Route::middleware('role:Super Admin')->prefix('admin')->group(function () {

    Route::get('/audit-log', [AdminController::class, 'auditLog']);
    
    // Manage Departments Page
    Route::get('/manage-departments', [AdminController::class, 'manage_departments'])->name('admin.departments.index');
    Route::post('/manage-departments/assign', [AdminController::class, 'assignUser'])->name('admin.departments.assign');
    Route::put('/admin/manage-departments/{id}', [AdminController::class, 'updateDepartment']);

    
    // User Management
    Route::get('/manage-users', [AdminController::class, 'manage_users'])->name('admin.users.index');
    Route::post('/manage-users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::patch('/manage-users/{id}/toggle', [AdminController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::put('/admin/manage-users/{id}', [AdminController::class, 'update']);

    Route::get('/admin/resolved-tickets', [AdminController::class, 'resolvedTickets'])->name('admin.resolved_tickets');

    // AI Management
    Route::get('/ai-settings', [AiSettingsController::class, 'index'])->name('ai');
    Route::post('/ai-settings', [AiSettingsController::class, 'update'])->name('admin.settings.ai.update');

});

// routes/web.php
Route::get('/feedback/{id}/route', function ($id) {
    // Read and decode JSON file from public folder
    $jsonPath = public_path('sample_feedback.json');
    $feedbackList = json_decode(file_get_contents($jsonPath), true);

    // Find the feedback entry by ID
    $feedback = collect($feedbackList)->firstWhere('id', (int) $id);

    if (!$feedback) {
        abort(404, 'Feedback not found');
    }

    // Add suggested department (could be from model in future, here we use existing department)
    $feedback['suggested_department'] = $feedback['department'];

    $departments = ['Registrar', 'Finance', 'Library', 'Facilities', 'IT', 'Admissions'];

    return view('admin.route-feedback', compact('feedback', 'departments'));
});

