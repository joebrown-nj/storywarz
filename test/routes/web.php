<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Warz\WarBrowseController;
use App\Http\Controllers\Warz\WarManagementController;
use App\Http\Controllers\Warz\WarRoundController;
use App\Http\Controllers\Warz\WarStoryController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('warz')->group(function () {
        Route::controller(WarManagementController::class)->group(function () {
            Route::get('create', 'create')->name('warz.create');
            Route::get('edit/{war}', 'edit')->name('warz.edit');
            Route::patch('update/{war}', 'update')->name('warz.update');
            Route::post('store', 'store')->name('warz.store');
        });

        Route::controller(WarBrowseController::class)->group(function () {
            Route::get('/', 'index')->name('warz');
            Route::get('{war}', 'show')->name('warz.show');
            Route::get('{war}/summary', 'summary')->name('warz.showSummary');
            Route::get('{war}/next-story', 'nextStory')->name('warz.nextStory');
            Route::post('comment/{war}', 'comment')->name('warz.comment');
        });

        Route::controller(WarManagementController::class)->group(function () {
            Route::get('{war}/delete-warrior/{userId}', 'deleteWarrior')->name('warrior.delete');
        });

        Route::controller(WarStoryController::class)->group(function () {
            Route::get('{id}/removeStory', 'removeStory')->name('warz.removeStory');
            Route::get('{id}/add-story', 'addStoryForm')->name('warz.addStoryForm');
            Route::patch('{war}/addStories', 'addStories')->name('warz.addStories');
        });

        Route::post('{war}/vote', [WarRoundController::class, 'vote'])->name('warz.vote');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
