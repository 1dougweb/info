<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Member;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', fn() => redirect('/login'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Member area
Route::middleware('auth')->prefix('/')->group(function () {
    Route::get('/dashboard', [Member\DashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/products', [Member\ProductController::class, 'index'])->name('member.products.index');
    Route::get('/products/{slug}', [Member\ProductController::class, 'show'])->name('member.products.show');
    Route::get('/learn/{product:slug}/{lesson}', [Member\PlayerController::class, 'show'])->name('member.player');
    Route::post('/learn/{lesson}/complete', [Member\PlayerController::class, 'complete'])->name('member.lesson.complete');
    Route::get('/profile', [Member\ProfileController::class, 'edit'])->name('member.profile');
    Route::put('/profile', [Member\ProfileController::class, 'update'])->name('member.profile.update');
    // Comments
    Route::post('/lessons/{lesson}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
});

// Admin area
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', Admin\ProductController::class);

    // Modules (nested under product)
    Route::prefix('products/{product}/modules')->name('modules.')->group(function () {
        Route::get('/', [Admin\ModuleController::class, 'index'])->name('index');
        Route::post('/', [Admin\ModuleController::class, 'store'])->name('store');
        Route::put('/{module}', [Admin\ModuleController::class, 'update'])->name('update');
        Route::delete('/{module}', [Admin\ModuleController::class, 'destroy'])->name('destroy');

        // Lessons (nested under module)
        Route::prefix('{module}/lessons')->name('lessons.')->group(function () {
            Route::post('/', [Admin\LessonController::class, 'store'])->name('store');
            Route::put('/{lesson}', [Admin\LessonController::class, 'update'])->name('update');
            Route::delete('/{lesson}', [Admin\LessonController::class, 'destroy'])->name('destroy');
        });
    });

    // Users
    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::post('users/{user}/grant', [Admin\UserController::class, 'grantAccess'])->name('users.grant');
    Route::post('users/{user}/revoke', [Admin\UserController::class, 'revokeAccess'])->name('users.revoke');
    Route::put('users/{user}/role', [Admin\UserController::class, 'updateRole'])->name('users.role');

    // Webhooks
    Route::resource('webhooks', \App\Http\Controllers\CustomWebhookController::class)->except(['create', 'edit']);

    // Automations
    Route::get('automations', [Admin\AutomationController::class, 'index'])->name('automations.index');
    Route::post('automations', [Admin\AutomationController::class, 'store'])->name('automations.store');
    Route::put('automations/{automation}', [Admin\AutomationController::class, 'update'])->name('automations.update');
    Route::delete('automations/{automation}', [Admin\AutomationController::class, 'destroy'])->name('automations.destroy');
    Route::post('automations/{automation}/toggle', [Admin\AutomationController::class, 'toggle'])->name('automations.toggle');

    // Manual cron trigger (dev/admin use only)
    Route::post('cron/run', [Admin\AutomationController::class, 'runCron'])->name('cron.run');

    // Settings & SMTP
    Route::get('settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/smtp', [Admin\SettingsController::class, 'updateSmtp'])->name('settings.smtp.update');
    Route::post('settings/smtp/test', [Admin\SettingsController::class, 'testSmtp'])->name('settings.smtp.test');

    // Email Templates
    Route::get('email-templates', [Admin\EmailTemplateController::class, 'index'])->name('email-templates.index');
    Route::put('email-templates/{template}', [Admin\EmailTemplateController::class, 'update'])->name('email-templates.update');
});

// Temporary route to clear cache in production
Route::get('/limpar-cache-servidor', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return '<h1>Cache do Servidor Limpo com Sucesso!</h1><p>O Laravel na Hostinger agora está usando as configurações atualizadas e a fila Sync está ativada. Pode fechar esta tela e fazer um novo teste de Webhook!</p>';
});

// WAF-Bypassing Webhook Route (removed from API constraint)
Route::any('/webhooks/custom/{uuid}', [\App\Http\Controllers\CustomWebhookController::class, 'receivePayload'])->name('api.webhooks.custom');
