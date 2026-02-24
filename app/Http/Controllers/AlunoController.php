<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AlunoController extends Controller
{
    public function create(Request $request)
    {
        // Buscar o user que o middleware colocou no request
        $user = $request->attributes->get('user');

        return view('aluno-create', compact('user'));
    }

    public function store(Request $request)
    {
        dd(env('SUPABASE_SERVICE_ROLE'));
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE'),
        ])->post(env('SUPABASE_URL') . '/rest/v1/alunos', [
            'id' => $request->user_id,
            'nome' => $request->nome,
            'escola_instituicao' => $request->escola_instituicao,
            'ano_escolaridade' => $request->ano_escolaridade
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => $response->body()
            ], 400);
        }

        return response()->json([
            'success' => true,
            'aluno' => $response->json()
        ]);
    }
}
