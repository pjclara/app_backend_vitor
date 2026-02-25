<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SupabaseUserController;
use Illuminate\Support\Facades\Route;

// --- Páginas públicas ---

// redirect da raiz para o dashboard (ou login, se não autenticado)
Route::get('/', function () {
    return redirect('/admin');
});

// --- Auth (guests only) ---
Route::middleware('guest:supabase')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/create-user', function () {
        return view('create-user');
    })->name('create-user.form');
    Route::post('/create-user', [SupabaseUserController::class, 'create'])->name('create-user.submit');
});

// --- Auth (logout) ---
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- Rotas protegidas (requer autenticação) ---
Route::middleware('auth:supabase')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/alunos/criar', [AlunoController::class, 'create']);
    Route::post('/alunos', [AlunoController::class, 'store']);
});

// --- Rotas de teste/debug (remover em produção) ---
Route::get('/test-connection', [SupabaseUserController::class, 'testConnection']);
Route::get('/test-create-user', [SupabaseUserController::class, 'testCreateUser']);
Route::get('/fix-supabase', [SupabaseUserController::class, 'fixSupabaseSetup']);

