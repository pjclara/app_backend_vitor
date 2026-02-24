<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class SupabaseUserProvider implements UserProvider
{
    /**
     * Recuperar utilizador por ID (sub do JWT) a partir da sessão.
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        // O utilizador é reconstruído a partir da sessão pelo guard
        return null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        // Não aplicável com Supabase
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        // A autenticação é feita via Supabase, não por credenciais locais
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        // A validação é feita via JWT do Supabase
        return false;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        // Não aplicável com Supabase
    }
}
