<!DOCTYPE html>
<html>
<head>
    <title>Painel de Controle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header {
            background: white; padding: 20px; border-radius: 8px;
            margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .user-info { display: flex; justify-content: space-between; align-items: center; }
        .cards {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px; margin-bottom: 20px;
        }
        .card {
            background: white; padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h3 { margin: 0 0 10px 0; color: #666; font-size: 14px; }
        .card p { margin: 0; font-size: 24px; font-weight: bold; color: #333; }
        .logout-btn {
            background-color: #dc3545; color: white; padding: 8px 16px;
            border: none; border-radius: 4px; cursor: pointer;
        }
        .logout-btn:hover { background-color: #c82333; }
        .user-details {
            background: white; padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        pre {
            background: #f8f9fa; padding: 15px; border-radius: 4px;
            overflow-x: auto; font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-info">
                <div>
                    <h1>Dashboard</h1>
                    <p>Bem-vindo, {{ Auth::user()->nome ?: Auth::user()->email }}!</p>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Email</h3>
                <p style="font-size: 16px;">{{ Auth::user()->email }}</p>
            </div>
            <div class="card">
                <h3>ID (Supabase)</h3>
                <p style="font-size: 14px; word-break: break-all;">{{ Auth::user()->id }}</p>
            </div>
            <div class="card">
                <h3>Role</h3>
                <p>{{ Auth::user()->role }}</p>
            </div>
            @if(Auth::user()->nome)
            <div class="card">
                <h3>Nome</h3>
                <p style="font-size: 16px;">{{ Auth::user()->nome }}</p>
            </div>
            @endif
            @if(Auth::user()->escola_instituicao)
            <div class="card">
                <h3>Escola / Instituicao</h3>
                <p style="font-size: 16px;">{{ Auth::user()->escola_instituicao }}</p>
            </div>
            @endif
            @if(Auth::user()->ano_escolaridade)
            <div class="card">
                <h3>Ano de Escolaridade</h3>
                <p>{{ Auth::user()->ano_escolaridade }}º</p>
            </div>
            @endif
        </div>

        <div class="user-details">
            <h3>Dados completos do JWT</h3>
            <pre>{{ json_encode(Auth::user()->getClaims(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <script type="module">
    import { createClient } from 'https://esm.sh/@supabase/supabase-js';
    const supabase = createClient('{{ config('services.supabase.url') }}', '{{ config('services.supabase.anon_key') }}');

    // O logout no Supabase JS é feito pelo form POST ao Laravel,
    // mas também limpamos o lado do cliente
    document.querySelector('form[action="{{ route('logout') }}"]').addEventListener('submit', async function(e) {
        try {
            await supabase.auth.signOut();
            localStorage.clear();
            sessionStorage.clear();
            document.cookie = "sb_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
        } catch (err) {
            console.warn('Erro ao limpar sessão Supabase:', err);
        }
        // O form submit continua normalmente para o Laravel
    });
    </script>
</body>
</html>