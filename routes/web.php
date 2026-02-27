<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;

Route::get('/', [PageController::class, 'show'])->name('home');
Route::post('/inquiry', [PageController::class, 'storeInquiry'])->name('inquiry.store');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
