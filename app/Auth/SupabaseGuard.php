<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;

class SupabaseGuard implements Guard, StatefulGuard
{
    use GuardHelpers;

    protected Request $request;

    public function __construct(SupabaseUserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Retorna o utilizador autenticado.
     */
    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        // Recuperar da sessão Laravel
        $userData = session('supabase_auth_user');
        $token = session('supabase_token');

        if ($userData) {
            $this->user = new SupabaseUser((object) $userData, $token);
        }

        return $this->user;
    }

    /**
     * Validar credenciais (não usado diretamente, a validação é feita pelo JWT).
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * Login: guardar o utilizador na sessão.
     */
    public function login(Authenticatable $user, $remember = false): void
    {
        $this->user = $user;

        if ($user instanceof SupabaseUser) {
            session([
                'supabase_auth_user' => $user->getClaims(),
                'supabase_token' => $user->getToken(),
            ]);
        }
    }

    /**
     * Logout: limpar sessão.
     */
    public function logout(): void
    {
        $this->user = null;

        session()->forget('supabase_auth_user');
        session()->forget('supabase_token');
        session()->forget('supabase_user');
        session()->invalidate();
        session()->regenerateToken();
    }

    // --- StatefulGuard methods (needed by Filament) ---

    public function attempt(array $credentials = [], $remember = false): bool
    {
        // A autenticação é feita via Supabase API, não por credenciais locais
        return false;
    }

    public function once(array $credentials = []): bool
    {
        return false;
    }

    public function loginUsingId($id, $remember = false): Authenticatable|bool
    {
        return false;
    }

    public function onceUsingId($id): Authenticatable|bool
    {
        return false;
    }

    public function viaRemember(): bool
    {
        return false;
    }
}
