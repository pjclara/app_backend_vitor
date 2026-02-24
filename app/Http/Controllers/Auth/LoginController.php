<?php

namespace App\Http\Controllers\Auth;

use App\Auth\SupabaseGuard;
use App\Auth\SupabaseUser;
use App\Http\Controllers\Controller;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Mostrar formulário de login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('login');
    }

    /**
     * Receber o token JWT do Supabase (enviado pelo frontend JS)
     * e autenticar no Laravel.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->input('token');

        // Validar o JWT com JWKS do Supabase
        try {
            $decoded = $this->validateSupabaseToken($token);
        } catch (\Exception $e) {
            Log::error('Login: Falha na validação do token - ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Token inválido: ' . $e->getMessage(),
            ], 401);
        }

        // Criar SupabaseUser e fazer login no Laravel
        $user = new SupabaseUser($decoded, $token);

        /** @var SupabaseGuard $guard */
        $guard = Auth::guard('supabase');
        $guard->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login efetuado com sucesso',
            'redirect' => '/dashboard',
        ]);
    }

    /**
     * Logout — limpa sessão Laravel e retorna.
     */
    public function logout(Request $request): mixed
    {
        /** @var SupabaseGuard $guard */
        $guard = Auth::guard('supabase');
        $guard->logout();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect('/login');
    }

    /**
     * Validar JWT do Supabase usando JWKS.
     */
    protected function validateSupabaseToken(string $token): object
    {
        $segments = explode('.', $token);
        if (count($segments) !== 3) {
            throw new \Exception('Token com formato inválido (' . count($segments) . ' segmentos)');
        }

        // Buscar JWKS do Supabase (cache 1 hora)
        $jwks = Cache::remember('supabase_jwks', 3600, function () {
            $response = Http::withHeaders([
                'apikey' => config('services.supabase.anon_key'),
            ])->get(config('services.supabase.url') . '/auth/v1/.well-known/jwks.json');

            if ($response->failed()) {
                throw new \Exception('Falha ao buscar JWKS: HTTP ' . $response->status());
            }

            return $response->json();
        });

        $keys = JWK::parseKeySet($jwks);

        return JWT::decode($token, $keys);
    }
}
