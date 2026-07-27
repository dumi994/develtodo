<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $seedFile = storage_path('app/.seeded');
    if (!file_exists($seedFile)) {
        return redirect()->route('setup');
    }
    return redirect()->route('dashboard');
});

Route::get('/setup', [SetupController::class, 'index'])->name('setup');
Route::post('/setup', [SetupController::class, 'run'])->name('setup.run');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('tickets', TicketController::class);
    Route::resource('quotes', QuoteController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::post('/tasks/{task}/timer', [TaskController::class, 'updateTimer'])->name('tasks.timer');
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
