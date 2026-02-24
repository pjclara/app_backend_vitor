<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseAuth
{
    public function handle($request, Closure $next)
    {
        $token = null;

        // Tentar cookie primeiro
        $cookieToken = $request->cookie('sb_token');
        if ($cookieToken) {
            $cookieToken = urldecode($cookieToken);
            $segments = explode('.', $cookieToken);
            if (count($segments) === 3) {
                $token = $cookieToken;
            } else {
                Log::warning('SupabaseAuth: Cookie token inválido (' . count($segments) . ' segmentos), a tentar sessão...');
            }
        }

        // Fallback: verificar na sessão
        if (!$token) {
            $token = session('supabase_token');
            Log::info('SupabaseAuth: Token da sessão ' . ($token ? 'encontrado' : 'não encontrado'));
            if (!$token) {
                log::warning('SupabaseAuth: Nenhum token encontrado, redirecionando para login');
                return redirect('/login');
            }
        }


        try {
            // Buscar JWKS do Supabase (com cache de 1 hora)
            $jwks = Cache::remember('supabase_jwks', 3600, function () {
                $response = Http::withHeaders([
                    'apikey' => env('SUPABASE_ANON_KEY'),
                ])->get(env('SUPABASE_URL') . '/auth/v1/.well-known/jwks.json');

                if ($response->failed()) {
                    throw new \Exception('Falha ao buscar JWKS: ' . $response->status());
                }

                return $response->json();
            });

            // Converter JWKS para chaves utilizáveis
            $keys = JWK::parseKeySet($jwks);

            // Decodificar token com ES256
            $decoded = JWT::decode($token, $keys);

            $request->attributes->set('user', $decoded);

            
        } catch (\Exception $e) {
            Log::error('SupabaseAuth: ' . $e->getMessage());

            // Limpar cache JWKS caso esteja corrupto
            Cache::forget('supabase_jwks');

            return redirect('/login')->with('error', 'Token inválido');
        }

        return $next($request);
    }
}
