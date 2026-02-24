<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Http;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Ao editar, sincroniza alterações com o Supabase Auth.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $updateData = [];

        // Sincronizar email se alterado
        if (isset($data['email']) && $data['email'] !== $this->record->email) {
            $updateData['email'] = $data['email'];
        }

        // Sincronizar password se preenchida
        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        // Sincronizar metadata
        $updateData['user_metadata'] = [
            'name' => $data['name'] ?? $this->record->name,
            'role' => $data['role'] ?? $this->record->role?->value ?? 'aluno',
        ];

        if (!empty($updateData)) {
            $response = Http::withHeaders([
                'apikey' => config('services.supabase.anon_key'),
                'Authorization' => 'Bearer ' . config('services.supabase.service_role'),
                'Content-Type' => 'application/json',
            ])->put(config('services.supabase.url') . '/auth/v1/admin/users/' . $this->record->id, $updateData);

            if ($response->failed()) {
                $error = $response->json('msg') ?? $response->json('message') ?? 'Erro ao atualizar utilizador no Supabase';
                throw new \Exception($error);
            }
        }

        return $data;
    }
}
