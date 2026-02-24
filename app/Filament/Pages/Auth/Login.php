<?php

namespace App\Filament\Pages\Auth;

use App\Auth\SupabaseGuard;
use App\Auth\SupabaseUser;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $email = $data['email'];
        $password = $data['password'];

        // Autenticar contra a API do Supabase (server-side)
        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.supabase.url') . '/auth/v1/token?grant_type=password', [
            'email' => $email,
            'password' => $password,
        ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        $authData = $response->json();
        $token = $authData['access_token'] ?? null;

        if (!$token) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        // Validar o JWT com JWKS
        try {
            $decoded = $this->validateToken($token);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'data.email' => 'Erro na validação do token: ' . $e->getMessage(),
            ]);
        }

        // Criar SupabaseUser e fazer login
        $user = new SupabaseUser($decoded, $token);

        /** @var SupabaseGuard $guard */
        $guard = Auth::guard('supabase');
        $guard->login($user);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function validateToken(string $token): object
    {
        $jwks = Cache::remember('supabase_jwks', 3600, function () {
            $response = Http::withHeaders([
                'apikey' => config('services.supabase.anon_key'),
            ])->get(config('services.supabase.url') . '/auth/v1/.well-known/jwks.json');

            if ($response->failed()) {
                throw new \Exception('Falha ao buscar JWKS');
            }

            return $response->json();
        });

        $keys = JWK::parseKeySet($jwks);

        return JWT::decode($token, $keys);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }
}
