<?php

use App\Http\Controllers\SupabaseUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/logout', function () {
    return view('logout');
})->name('logout');

Route::middleware('supabase')->group(function () {
    Route::get('/dashboard', function (Illuminate\Http\Request $request) {
        $user = $request->attributes->get('user');
        return view('dashboard', ['user' => $user]);
    });
});

Route::get('/create-user', function () {
    return view('create-user');
})->name('create-user.form');

Route::post('/create-user', [SupabaseUserController::class, 'create']);

Route::get('/test-connection', [SupabaseUserController::class, 'testConnection']);

Route::get('/test-create-user', [SupabaseUserController::class, 'testCreateUser']);

Route::get('/test-auth', function () {
    return view('test-auth');
})->name('test-auth');

Route::get('/fix-supabase', [SupabaseUserController::class, 'fixSupabaseSetup']);

Route::get('/debug-token', function (Illuminate\Http\Request $request) {
    return response()->json([
        'cookies' => $request->cookies->all(),
        'sb_token_exists' => $request->hasCookie('sb_token'),
        'sb_token_value' => $request->cookie('sb_token') ? substr($request->cookie('sb_token'), 0, 50) . '...' : null,
        'jwt_secret_configured' => env('SUPABASE_JWT_SECRET') ? 'Yes' : 'No',
        'headers' => [
            'host' => $request->header('host'),
            'user-agent' => $request->header('user-agent'),
            'cookie' => $request->header('cookie') ? substr($request->header('cookie'), 0, 100) . '...' : null,
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});

Route::get('/dashboard-test', function () {
    return view('dashboard', ['user' => (object)['email' => 'test@example.com', 'sub' => 'test-id']]);
})->name('dashboard-test');

Route::post('/save-token', function (Illuminate\Http\Request $request) {
    $token = $request->input('token');
    $user = $request->input('user');
    
    if ($token) {
        // Salvar na sessão como backup
        session(['supabase_token' => $token]);
        session(['supabase_user' => $user]);
        
        return response()->json(['success' => true, 'message' => 'Token salvo na sessão']);
    }
    
    return response()->json(['success' => false, 'message' => 'Token não fornecido'], 400);
})->name('save-token');

Route::get('/debug-login', function () {
    return view('debug-login');
})->name('debug-login');
