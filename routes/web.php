<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TagController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});
Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Posts
Route::get('posts/my', [PostController::class, 'indexSubscribed'])
    ->middleware(['auth'])->name('posts.indexSubscribed');

Route::get('posts/by-tag', [PostController::class, 'indexByTag'])
    ->middleware(['auth'])->name('posts.indexByTag');

Route::get('posts/private', [PostController::class, 'indexPrivate'])
    ->middleware(['auth'])->name('posts.indexPrivate');

Route::post('posts/{post}/grand-access', [PostController::class, 'grandAccess'])
    ->middleware(['auth'])->name('posts.grandAccess');

Route::resource('posts', PostController::class);

// Tags
Route::resource('tags', TagController::class)->only([
    'index',
    'store',
]);

// Subscriptions
Route::post('subscription/destroy-by-users', [SubscriptionController::class, 'destroyByUsers'])
    ->middleware(['auth'])->name('subscription.destroyByUsers');

Route::resource('subscription', SubscriptionController::class)
    ->only(['store'])
    ->middleware(['auth']);

//Comments
Route::resource('comments', CommentController::class)->only([
    'store',
    'destroy',
]);

require __DIR__.'/auth.php';
