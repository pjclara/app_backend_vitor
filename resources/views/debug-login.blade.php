<!DOCTYPE html>
<html>
<head>
    <title>Test Login and Debug</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .step {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            white-space: pre-wrap;
            font-family: monospace;
            max-height: 200px;
            overflow-y: auto;
        }
        input {
            width: 200px;
            padding: 8px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <h1>🔧 Debug Login e Dashboard</h1>
    
    <div class="step">
        <h3>Passo 1: Login Test</h3>
        <input type="email" id="email" placeholder="Email" value="test@example.com">
        <input type="password" id="password" placeholder="Password" value="senha123">
        <br>
        <button onclick="testLogin()">Fazer Login Supabase</button>
        <div id="login-result" class="result"></div>
    </div>

    <div class="step">
        <h3>Passo 2: Verificar Token</h3>
        <button onclick="checkToken()">Verificar Token no Browser</button>
        <button onclick="testTokenEndpoint()">Testar Endpoint Debug</button>
        <div id="token-result" class="result"></div>
    </div>

    <div class="step">
        <h3>Passo 3: Testar Dashboard</h3>
        <button onclick="testDashboard()">Ir para Dashboard</button>
        <button onclick="testDashboardFetch()">Fetch Dashboard</button>
        <div id="dashboard-result" class="result"></div>
    </div>

    <div class="step">
        <h3>Passo 4: Salvar na Sessão</h3>
        <button onclick="saveToSession()">Salvar Token na Sessão Laravel</button>
        <div id="session-result" class="result"></div>
    </div>

    <script type="module">
    import { createClient } from 'https://esm.sh/@supabase/supabase-js';

    const supabase = createClient('{{ env('SUPABASE_URL') }}', '{{ env('SUPABASE_ANON_KEY') }}');
    
    let currentToken = null;

    window.testLogin = async function() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const resultDiv = document.getElementById('login-result');
        
        try {
            resultDiv.textContent = "🔄 Fazendo login...";
            
            const { data, error } = await supabase.auth.signInWithPassword({
                email: email,
                password: password
            });

            if (error) {
                resultDiv.textContent = `❌ Erro: ${error.message}`;
                return;
            }

            currentToken = data.session.access_token;
            const expires = new Date(data.session.expires_at * 1000).toUTCString();
            
            // Múltiplos métodos para salvar o token
            document.cookie = `sb_token=${currentToken}; path=/; expires=${expires}`;
            document.cookie = `sb_token=${currentToken}; path=/; SameSite=Lax`;
            document.cookie = `sb_token=${currentToken}; path=/`;
            
            resultDiv.textContent = `✅ Login realizado!
User: ${data.user.email}
Token: ${currentToken.substring(0, 30)}...
Expira: ${expires}
Cookie definido: sb_token`;

        } catch (error) {
            resultDiv.textContent = `❌ Exceção: ${error.message}`;
        }
    };

    window.checkToken = function() {
        const resultDiv = document.getElementById('token-result');
        const cookies = document.cookie;
        const sbToken = getCookie('sb_token');
        
        resultDiv.textContent = `Cookies: ${cookies}

Token sb_token: ${sbToken ? sbToken.substring(0, 50) + '...' : 'NÃO ENCONTRADO'}

Current token in memory: ${currentToken ? currentToken.substring(0, 50) + '...' : 'NENHUM'}`;
    };

    window.testTokenEndpoint = async function() {
        const resultDiv = document.getElementById('token-result');
        
        try {
            const response = await fetch('/debug-token');
            const data = await response.json();
            resultDiv.textContent = JSON.stringify(data, null, 2);
        } catch (error) {
            resultDiv.textContent = `Erro: ${error.message}`;
        }
    };

    window.testDashboard = function() {
        window.location.href = '/dashboard';
    };

    window.testDashboardFetch = async function() {
        const resultDiv = document.getElementById('dashboard-result');
        
        try {
            const response = await fetch('/dashboard');
            const text = await response.text();
            
            if (response.redirected) {
                resultDiv.textContent = `❌ Redirecionado para: ${response.url}
Status: ${response.status}`;
            } else {
                resultDiv.textContent = `✅ Dashboard carregado!
Status: ${response.status}
Content length: ${text.length} chars`;
            }
        } catch (error) {
            resultDiv.textContent = `Erro: ${error.message}`;
        }
    };

    window.saveToSession = async function() {
        const resultDiv = document.getElementById('session-result');
        
        if (!currentToken) {
            resultDiv.textContent = "❌ Faça login primeiro para obter um token";
            return;
        }

        try {
            const response = await fetch('/save-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    token: currentToken
                })
            });

            const data = await response.json();
            resultDiv.textContent = JSON.stringify(data, null, 2);
        } catch (error) {
            resultDiv.textContent = `Erro: ${error.message}`;
        }
    };

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
    </script>

    <p><a href="/login">← Voltar para Login Normal</a></p>
</body>
</html>