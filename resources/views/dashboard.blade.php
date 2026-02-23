<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
        }
        .card p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .user-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-info">
                <div>
                    <h1>🏠 Dashboard</h1>
                    <p>Bem-vindo ao painel de controle!</p>
                </div>
                <div>
                    <button onclick="performLogout()" class="logout-btn">🚪 Logout</button>
                </div>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <h3>👤 Status do Usuário</h3>
                <p id="user-status">Carregando...</p>
            </div>
            <div class="card">
                <h3>🔑 Token Status</h3>
                <p id="token-status">Verificando...</p>
            </div>
            <div class="card">
                <h3>⏰ Sessão</h3>
                <p id="session-time">Ativa</p>
            </div>
            <div class="card">
                <h3>🔧 Ações</h3>
                <p><a href="/test-auth" style="color: #007bff;">Testar Auth</a></p>
            </div>
        </div>

        <div class="user-details">
            <h3>📋 Detalhes do Usuário (Supabase)</h3>
            <div id="user-info">
                @if(isset($user))
                    <p><strong>Dados do Token JWT:</strong></p>
                    <pre>{{ json_encode($user, JSON_PRETTY_PRINT) }}</pre>
                @else
                    <p>❌ Dados do usuário não encontrados</p>
                @endif
            </div>
        </div>
    </div>

    <script type="module">
    import { createClient } from 'https://esm.sh/@supabase/supabase-js';

    const supabase = createClient('{{ env('SUPABASE_URL') }}', '{{ env('SUPABASE_ANON_KEY') }}');

    async function loadUserInfo() {
        try {
            // Verificar sessão do Supabase
            const { data: { session } } = await supabase.auth.getSession();
            
            if (session) {
                document.getElementById('user-status').textContent = session.user.email;
                document.getElementById('token-status').textContent = '✅ Válido';
                document.getElementById('session-time').textContent = new Date(session.expires_at * 1000).toLocaleString();
            } else {
                document.getElementById('user-status').textContent = '❌ Sem sessão';
                document.getElementById('token-status').textContent = '❌ Inválido';
            }
        } catch (error) {
            console.error('Erro ao carregar dados do usuário:', error);
            document.getElementById('user-status').textContent = '❌ Erro';
        }
    }

    // Carregar informações ao iniciar a página
    loadUserInfo();

    // Função de logout direta
    window.performLogout = async function() {
        try {
            // 1. Logout no Supabase
            await supabase.auth.signOut();
            
            // 2. Limpar cookies
            document.cookie = "sb_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
            
            // 3. Limpar localStorage/sessionStorage
            localStorage.clear();
            sessionStorage.clear();
            
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
            
            // 5. Redirecionar
            window.location.href = '/login';
            
        } catch (error) {
            console.error('Erro no logout:', error);
            // Mesmo com erro, limpar e redirecionar
            document.cookie = "sb_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
            window.location.href = '/login';
        }
    };
    </script>
</body>
</html>
            <h3 class="text-gray-500 text-sm font-medium">Growth</h3>
            <p class="text-3xl font-bold text-green-600 mt-2">+12.5%</p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Activity</h2>
        <p class="text-gray-600">No recent activity yet.</p>
    </div>
</div>