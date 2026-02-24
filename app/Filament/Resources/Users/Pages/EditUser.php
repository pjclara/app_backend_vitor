<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Http;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Ao editar um utilizador, sincroniza as alterações com Supabase Auth.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $password = $data['password'] ?? null;
        
        // Sincronizar com Supabase Auth
        $updateData = [
            'email' => $data['email'],
            'user_metadata' => [
                'name' => $data['name'],
                'role' => $data['role'] ?? 'aluno',
            ],
        ];
        
        // Adicionar password apenas se foi fornecida
        if ($password) {
            $updateData['password'] = $password;
        }
        
        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Authorization' => 'Bearer ' . config('services.supabase.service_role'),
            'Content-Type' => 'application/json',
        ])->put(config('services.supabase.url') . '/auth/v1/admin/users/' . $this->record->id, $updateData);

        if ($response->failed()) {
            $error = $response->json('msg') ?? $response->json('message') ?? 'Erro ao atualizar utilizador no Supabase';
            throw new \Exception($error);
        }

        // Remover password antes de guardar na base de dados local
        unset($data['password']);
        
        return $data;
    }
}
