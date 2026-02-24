<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class SupabaseGuard implements Guard
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
    public function login(SupabaseUser $user): void
    {
        $this->user = $user;

        session([
            'supabase_auth_user' => $user->getClaims(),
            'supabase_token' => $user->getToken(),
        ]);
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
}
