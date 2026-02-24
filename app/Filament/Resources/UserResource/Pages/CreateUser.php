<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Ao criar um utilizador, cria primeiro no Supabase Auth e usa o UUID retornado.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Criar utilizador no Supabase Auth
        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Authorization' => 'Bearer ' . config('services.supabase.service_role'),
            'Content-Type' => 'application/json',
        ])->post(config('services.supabase.url') . '/auth/v1/admin/users', [
            'email' => $data['email'],
            'password' => $data['password'],
            'email_confirm' => true,
            'user_metadata' => [
                'name' => $data['name'],
                'role' => $data['role'] ?? 'aluno',
            ],
        ]);

        if ($response->failed()) {
            $error = $response->json('msg') ?? $response->json('message') ?? 'Erro ao criar utilizador no Supabase';
            throw new \Exception($error);
        }

        $supabaseUser = $response->json();

        // Usar o UUID do Supabase como ID do utilizador local
        $data['id'] = $supabaseUser['id'];

        return $data;
    }
}
