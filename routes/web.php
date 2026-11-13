<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FilmController,
    AuthController,
    AdminController,
    KritikController,
};

// Route Film & Publik
Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');
Route::post('/movies/{film}/comments', [KritikController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

// Route Tamu (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Route Logout (Autentikasi)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Route Administrator
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/casts', [AdminController::class, 'castList'])->name('admin.casts');
    Route::get('/admin/users', [AdminController::class, 'userList'])->name('admin.users');
    Route::delete('/admin/users/{user}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');
    Route::get('/admin/comments', [AdminController::class, 'commentList'])->name('admin.comments');
    Route::delete('/admin/comments/{kritik}', [AdminController::class, 'commentDestroy'])->name('admin.comments.destroy');
    Route::get('/admin/films', [AdminController::class, 'filmList'])->name('admin.films');
    Route::get('/admin/films/create', [AdminController::class, 'filmCreate'])->name('admin.films.create');
    Route::post('/admin/films', [AdminController::class, 'filmStore'])->name('admin.films.store');
    Route::get('/admin/films/{film}/edit', [AdminController::class, 'filmEdit'])->name('admin.films.edit');
    Route::put('/admin/films/{film}', [AdminController::class, 'filmUpdate'])->name('admin.films.update');
    Route::delete('/admin/films/{film}', [AdminController::class, 'filmDestroy'])->name('admin.films.destroy');
    Route::get('/admin/films/{film}/cast', [AdminController::class, 'filmCast'])->name('admin.film.cast');
    Route::post('/admin/films/{film}/cast', [AdminController::class, 'filmCastStore'])->name('admin.film.cast.store');
    Route::delete('/admin/films/{film}/cast/{peran}', [AdminController::class, 'filmCastDestroy'])->name('admin.film.cast.destroy');
});
