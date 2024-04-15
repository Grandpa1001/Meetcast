<?php

use App\Livewire\Home;
use App\Livewire\Chat\Index;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/app',Home::class)->middleware(['auth'])->name('app');
Route::get('/app/chat',Index::class)->middleware(['auth'])->name('chat.index');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
