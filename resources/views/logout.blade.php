<!DOCTYPE html>
<html>

<head>
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }

        .logout-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }

        .status {
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .loading {
            background-color: #cce7ff;
            color: #004085;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="logout-container">
        <h2>🚪 Logout</h2>
        <div id="status" class="status loading">
            🔄 Fazendo logout...
        </div>

        <div id="actions" style="display: none;">
            <p><a href="/login">← Voltar ao Login</a></p>
            <p><a href="/">🏠 Página Inicial</a></p>
        </div>
    </div>

    <script type="module">
        import {
            createClient
        } from 'https://esm.sh/@supabase/supabase-js';

        const supabase = createClient('{{ config('services.supabase.url') }}', '{{ config('services.supabase.anon_key') }}');
        console.log('Supabase client criado para logout');
        async function performLogout() {
            try {
                // 1. Logout no Supabase
                await supabase.auth.signOut({
                    scope: 'global'
                });

                // 2. Remover cookies
                document.cookie = "sb_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
                document.cookie = "laravel_session=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";

                // 3. Limpar localStorage/sessionStorage se houver
                localStorage.clear();
                sessionStorage.clear();

                sessionStorage.removeItem('supabase_token');


                // 4. Limpar sessão Laravel via fetch
                try {
                    await fetch('/clear-session', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                } catch (e) {
                    console.warn('Falha ao limpar sessão Laravel:', e);
                }

                const {
                    data
                } = await supabase.auth.getSession();
                console.log(data);

                // 5. Mostrar sucesso
                document.getElementById('status').className = 'status success';
                document.getElementById('status').innerHTML =
                    '✅ Logout realizado com sucesso!<br><small>Redirecionando em 2 segundos...</small>';
                document.getElementById('actions').style.display = 'block';

                // 6. Redirecionar após 2 segundos
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);

            } catch (error) {
                console.error('Erro no logout:', error);

                // Mesmo com erro, fazer limpeza básica
                document.cookie = "sb_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
                document.cookie = "laravel_session=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
                localStorage.clear();
                sessionStorage.clear();

                document.getElementById('status').className = 'status success';
                document.getElementById('status').innerHTML =
                    '✅ Logout realizado<br><small>(com alguns avisos - redirecionando...)</small>';
                document.getElementById('actions').style.display = 'block';

                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            }
        }

        // Executar logout automaticamente ao carregar a página
        performLogout();
    </script>
</body>

</html>
