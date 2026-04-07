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
        Route::controller(WarBrowseController::class)->group(function () {
            Route::get('/', 'index')->name('warz');
            Route::get('{id}', 'show')->name('warz.show');
            Route::get('{id}/summary', 'summary')->name('warz.showSummary');
            Route::get('{id}/next-story', 'nextStory')->name('warz.nextStory');
            Route::post('comment/{id}', 'comment')->name('warz.comment');
        });

        Route::controller(WarManagementController::class)->group(function () {
            Route::get('create', 'create')->name('warz.create');
            Route::get('edit/{id}', 'edit')->name('warz.edit');
            Route::patch('update/{id}', 'update')->name('warz.update');
            Route::get('{warId}/delete-warrior/{userId}', 'deleteWarrior')->name('warrior.delete');
        });

        Route::controller(WarStoryController::class)->group(function () {
            Route::get('{id}/removeStory', 'removeStory')->name('warz.removeStory');
            Route::get('{id}/add-story', 'addStoryForm')->name('warz.addStoryForm');
            Route::patch('{id}/addStories', 'addStories')->name('warz.addStories');
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
