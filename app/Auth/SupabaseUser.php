<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class SupabaseUser implements Authenticatable
{
    protected string $id;
    protected string $email;
    protected string $role;
    protected array $userMetadata;
    protected array $appMetadata;
    protected ?string $token;
    protected array $claims;

    public function __construct(object $jwtPayload, ?string $token = null)
    {
        $this->id = $jwtPayload->sub ?? '';
        $this->email = $jwtPayload->email ?? '';
        $this->role = $jwtPayload->role ?? 'authenticated';
        $this->userMetadata = (array) ($jwtPayload->user_metadata ?? []);
        $this->appMetadata = (array) ($jwtPayload->app_metadata ?? []);
        $this->token = $token;
        $this->claims = (array) $jwtPayload;
    }

    // --- Authenticatable interface ---

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // Supabase não usa remember token
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    // --- Helpers ---

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getNome(): string
    {
        return $this->userMetadata['nome'] ?? $this->userMetadata['name'] ?? '';
    }

    public function getEscolaInstituicao(): string
    {
        return $this->userMetadata['escola_instituicao'] ?? '';
    }

    public function getAnoEscolaridade(): ?int
    {
        return $this->userMetadata['ano_escolaridade'] ?? null;
    }

    public function getUserMetadata(): array
    {
        return $this->userMetadata;
    }

    public function getAppMetadata(): array
    {
        return $this->appMetadata;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }

    /**
     * Permite aceder a propriedades como $user->email, $user->nome, etc.
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'id' => $this->id,
            'email' => $this->email,
            'role' => $this->role,
            'nome' => $this->getNome(),
            'name' => $this->getNome(),
            'escola_instituicao' => $this->getEscolaInstituicao(),
            'ano_escolaridade' => $this->getAnoEscolaridade(),
            'token' => $this->token,
            default => $this->userMetadata[$name] ?? $this->claims[$name] ?? null,
        };
    }
}
