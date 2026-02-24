<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SupabaseUserController extends Controller
{
    public function create(Request $request)
    {
        // Validar os dados de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'nome' => 'required|string|max:255',
            'escola_instituicao' => 'required|string|max:255',
            'ano_escolaridade' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // 1. Criar utilizador no Supabase Auth
        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Authorization' => 'Bearer ' . config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.supabase.url') . '/auth/v1/signup', [
            'email' => $request->email,
            'password' => $request->password,
            'data' => [
                'nome' => $request->nome,
                'escola_instituicao' => $request->escola_instituicao,
                'ano_escolaridade' => (int) $request->ano_escolaridade,
            ]
        ]);

        if ($response->failed()) {
            $error = $response->json();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar utilizador',
                    'error' => $error
                ], 400);
            }
            
            return back()->withErrors(['error' => 'Erro ao criar utilizador: ' . ($error['message'] ?? 'Erro desconhecido')])->withInput();
        }

        $authUser = $response->json();
        $userId = $authUser['id'] ?? ($authUser['user']['id'] ?? null);

        // 2. Inserir dados na tabela users
        if ($userId) {
            $userResponse = Http::withHeaders([
                'apikey' => config('services.supabase.anon_key'),
                'Authorization' => 'Bearer ' . config('services.supabase.service_role'),
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation',
            ])->post(config('services.supabase.url') . '/rest/v1/users', [
                'id' => $userId,
                'nome' => $request->nome,
                'email' => $request->email,
                'escola_instituicao' => $request->escola_instituicao,
                'ano_escolaridade' => (int) $request->ano_escolaridade,
            ]);

            if ($userResponse->failed()) {
                $userError = $userResponse->json();
                
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilizador auth criado, mas erro ao guardar dados do user',
                        'error' => $userError
                    ], 400);
                }
                
                return back()->withErrors(['error' => 'Utilizador criado, mas erro ao guardar dados do user: ' . ($userError['message'] ?? json_encode($userError))])->withInput();
            }
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'user criado com sucesso!',
                'user' => $authUser
            ]);
        }

        return back()->with('success', 'user criado com sucesso!');
    }

    public function testConnection()
    {
        // Testar variáveis de ambiente
        $url = env('SUPABASE_URL');
        $serviceRole = env('SUPABASE_SERVICE_ROLE');
        $anonKey = env('SUPABASE_ANON_KEY');
        $jwtSecret = env('SUPABASE_JWT_SECRET');

        $status = [
            'config' => [
                'SUPABASE_URL' => $url ? '✅ Configurado' : '❌ Não configurado',
                'SUPABASE_SERVICE_ROLE' => $serviceRole ? '✅ Configurado' : '❌ Não configurado',
                'SUPABASE_ANON_KEY' => $anonKey ? '✅ Configurado' : '❌ Não configurado',
                'SUPABASE_JWT_SECRET' => $jwtSecret ? '✅ Configurado' : '❌ Não configurado',
            ],
            'tests' => []
        ];

        // Teste 1: Verificar se a URL do Supabase responde
        if ($url) {
            try {
                $response = Http::timeout(10)->get($url);
                $status['tests']['url_response'] = $response->successful() 
                    ? '✅ URL responde' 
                    : '❌ URL não responde (' . $response->status() . ')';
            } catch (\Exception $e) {
                $status['tests']['url_response'] = '❌ Erro ao conectar: ' . $e->getMessage();
            }
        }

        // Teste 2: Testar autenticação admin
        if ($url && $serviceRole) {
            try {
                $response = Http::withHeaders([
                    'apikey' => $serviceRole,
                    'Authorization' => 'Bearer ' . $serviceRole,
                    'Content-Type' => 'application/json',
                ])->timeout(10)->get($url . '/auth/v1/admin/users?page=1&per_page=1');
                
                $status['tests']['admin_auth'] = $response->successful() 
                    ? '✅ Autenticação admin funcional' 
                    : '❌ Falha na autenticação admin (' . $response->status() . ')';
                
                if (!$response->successful()) {
                    $error = $response->json();
                    $status['tests']['admin_error'] = $error['message'] ?? 'Erro desconhecido';
                }
            } catch (\Exception $e) {
                $status['tests']['admin_auth'] = '❌ Erro na autenticação admin: ' . $e->getMessage();
            }
        }

        return response()->json($status, 200, [], JSON_PRETTY_PRINT);
    }

    public function testCreateUser()
    {
        $testEmail = 'teste' . time() . '@exemplo.com';
        $testPassword = 'senha123';

        $results = [];

        // Teste 1: Endpoint público com confirmação desabilitada
        try {
            $response1 = Http::withHeaders([
                'apikey' => env('SUPABASE_ANON_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('SUPABASE_URL') . '/auth/v1/signup', [
                'email' => $testEmail,
                'password' => $testPassword,
                'options' => [
                    'emailRedirectTo' => null,
                    'data' => [
                        'name' => 'Teste Usuario'
                    ]
                ]
            ]);

            $results['test_1'] = [
                'method' => 'POST /auth/v1/signup (sem emailRedirectTo)',
                'status_code' => $response1->status(),
                'success' => $response1->successful(),
                'response' => $response1->json()
            ];
        } catch (\Exception $e) {
            $results['test_1'] = [
                'method' => 'POST /auth/v1/signup',
                'error' => $e->getMessage()
            ];
        }

        // Teste 2: Endpoint admin (fallback)
        try {
            $response2 = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_ROLE'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE'),
                'Content-Type' => 'application/json',
            ])->post(env('SUPABASE_URL') . '/auth/v1/admin/users', [
                'email' => $testEmail . '.admin',
                'password' => $testPassword,
                'email_confirm' => true,
                'user_metadata' => [
                    'name' => 'Teste Admin'
                ]
            ]);

            $results['test_2'] = [
                'method' => 'POST /auth/v1/admin/users',
                'status_code' => $response2->status(),
                'success' => $response2->successful(),
                'response' => $response2->json()
            ];
        } catch (\Exception $e) {
            $results['test_2'] = [
                'method' => 'POST /auth/v1/admin/users',
                'error' => $e->getMessage()
            ];
        }

        // Teste 3: Verificar configurações de Auth
        try {
            $response3 = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_ROLE'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE'),
            ])->get(env('SUPABASE_URL') . '/auth/v1/settings');

            $results['test_3'] = [
                'method' => 'GET /auth/v1/settings',
                'status_code' => $response3->status(),
                'success' => $response3->successful(),
                'response' => $response3->json()
            ];
        } catch (\Exception $e) {
            $results['test_3'] = [
                'method' => 'GET /auth/v1/settings',
                'error' => $e->getMessage()
            ];
        }

        return response()->json([
            'test_email' => $testEmail,
            'diagnostics' => $results,
            'recommendations' => [
                'Verificar se "Enable email confirmations" está DESABILITADO no Supabase Dashboard',
                'Verificar se "Enable signup" está HABILITADO',
                'Verificar políticas RLS na tabela auth.users',
                'Tentar criar usuário diretamente no Supabase Dashboard primeiro'
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public function fixSupabaseSetup()
    {
        $instructions = [
            'problem' => 'Database error saving new user - indica problema de configuração no Supabase',
            'solution_steps' => [
                '1. Acessar o Dashboard do Supabase: https://supabase.com/dashboard/project/' . str_replace(['https://', '.supabase.co'], '', env('SUPABASE_URL')),
                '2. Ir para Authentication > Settings',
                '3. Configurar Email Templates > Confirmation: DESABILITAR "Enable email confirmations"',
                '4. Ir para Database > Tables',
                '5. Procurar tabla "profiles" (se existir)',
                '6. Verificar Row Level Security (RLS)',
                '7. SQL para resolver (executar no SQL Editor):'
            ],
            'sql_fixes' => [
                '-- Desabilitar RLS temporariamente para auth.users',
                'ALTER TABLE auth.users DISABLE ROW LEVEL SECURITY;',
                '',
                '-- Criar tabela profiles se não existir (comum em projetos Supabase)',
                'CREATE TABLE IF NOT EXISTS public.profiles (',
                '  id UUID REFERENCES auth.users ON DELETE CASCADE PRIMARY KEY,',
                '  email TEXT,',
                '  name TEXT,',
                '  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()',
                ');',
                '',
                '-- Habilitar RLS para profiles',
                'ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;',
                '',
                '-- Política para permitir que usuários vejam e editem seu próprio perfil',
                'CREATE POLICY "Users can view own profile" ON public.profiles',
                '  FOR SELECT USING (auth.uid() = id);',
                '',
                'CREATE POLICY "Users can update own profile" ON public.profiles',
                '  FOR UPDATE USING (auth.uid() = id);',
                '',
                '-- Função para criar perfil automaticamente',
                'CREATE OR REPLACE FUNCTION public.handle_new_user()',
                'RETURNS TRIGGER AS $$',
                'BEGIN',
                '  INSERT INTO public.profiles (id, email, name)',
                '  VALUES (new.id, new.email, new.raw_user_meta_data->>\'name\');',
                '  RETURN new;',
                'END;',
                '$$ LANGUAGE plpgsql SECURITY DEFINER;',
                '',
                '-- Trigger para criar perfil quando usuário é criado',
                'DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;',
                'CREATE TRIGGER on_auth_user_created',
                '  AFTER INSERT ON auth.users',
                '  FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();',
                '',
                '-- Reabilitar RLS para auth.users apenas se necessário',
                '-- ALTER TABLE auth.users ENABLE ROW LEVEL SECURITY;'
            ],
            'alternative_approach' => [
                'Se o problema persistir:',
                '1. Criar novo projeto Supabase',
                '2. Usar template "Blog" ou "Todo" que já vem configurado',
                '3. Copiar as chaves para o .env Laravel'
            ],
            'test_after_fix' => 'Após executar o SQL, teste em: ' . url('/test-auth')
        ];

        return response()->json($instructions, 200, [], JSON_PRETTY_PRINT);
    }
}
