<!DOCTYPE html>
<html>
<head>
    <title>Teste Supabase Auth</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
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
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        input {
            width: 200px;
            padding: 8px;
            margin: 5px;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🧪 Teste Supabase Authentication</h1>

    <div class="test-section">
        <h3>1. Testar Conexão</h3>
        <button onclick="testConnection()">Testar Conexão</button>
        <div id="connection-status"></div>
    </div>

    <div class="test-section">
        <h3>2. Criar Usuário (supabase.auth.signUp)</h3>
        <input type="email" id="test-email" placeholder="Email" value="">
        <input type="password" id="test-password" placeholder="Senha" value="senha123">
        <br>
        <button onclick="testSignUp()">Criar Usuário</button>
        <div id="signup-status"></div>
    </div>

    <div class="test-section">
        <h3>3. Login Usuário (supabase.auth.signInWithPassword)</h3>
        <input type="email" id="login-email" placeholder="Email">
        <input type="password" id="login-password" placeholder="Senha" value="senha123">
        <br>
        <button onclick="testSignIn()">Fazer Login</button>
        <div id="signin-status"></div>
    </div>

    <div class="test-section">
        <h3>4. Sessão Atual</h3>
        <button onclick="checkSession()">Verificar Sessão</button>
        <button onclick="logout()">Logout</button>
        <div id="session-status"></div>
    </div>

    <div class="test-section">
        <h3>5. Log de Eventos</h3>
        <button onclick="clearLog()">Limpar Log</button>
        <pre id="log"></pre>
    </div>

    <script type="module">
    import { createClient } from 'https://esm.sh/@supabase/supabase-js';

    const supabase = createClient('{{ env('SUPABASE_URL') }}', '{{ env('SUPABASE_ANON_KEY') }}');

    // Funções disponíveis globalmente
    window.supabase = supabase;

    function log(message) {
        const logElement = document.getElementById('log');
        const timestamp = new Date().toLocaleTimeString();
        logElement.textContent += `[${timestamp}] ${message}\n`;
        logElement.scrollTop = logElement.scrollHeight;
        console.log(message);
    }

    window.testConnection = async function() {
        const statusDiv = document.getElementById('connection-status');
        try {
            statusDiv.innerHTML = '<div class="status">🔄 Testando conexão...</div>';
            
            // Teste simples - verificar se o cliente foi criado
            const config = supabase.supabaseUrl;
            log(`Supabase URL: ${config}`);
            
            statusDiv.innerHTML = '<div class="status success">✅ Cliente Supabase inicializado com sucesso</div>';
        } catch (error) {
            log(`Erro na conexão: ${error.message}`);
            statusDiv.innerHTML = `<div class="status error">❌ Erro: ${error.message}</div>`;
        }
    };

    window.testSignUp = async function() {
        const email = document.getElementById('test-email').value || `teste${Date.now()}@exemplo.com`;
        const password = document.getElementById('test-password').value;
        const statusDiv = document.getElementById('signup-status');

        try {
            statusDiv.innerHTML = '<div class="status">🔄 Criando usuário...</div>';
            log(`Tentando criar usuário: ${email}`);

            // Primeira tentativa: SignUp padrão
            const { data, error } = await supabase.auth.signUp({
                email: email,
                password: password,
                options: {
                    data: {
                        created_from: 'test_page'
                    }
                }
            });

            if (error) {
                log(`❌ Erro SignUp padrão: ${error.message}`);
                
                // Segunda tentativa: SignUp sem metadata
                log(`🔄 Tentativa 2: SignUp sem metadata`);
                const { data: data2, error: error2 } = await supabase.auth.signUp({
                    email: email,
                    password: password
                });
                
                if (error2) {
                    log(`❌ Erro SignUp sem metadata: ${error2.message}`);
                    
                    // Terceira tentativa: SignUp com configurações mínimas
                    log(`🔄 Tentativa 3: SignUp com confirmação explícita`);
                    const { data: data3, error: error3 } = await supabase.auth.signUp({
                        email: email,
                        password: password,
                        options: {
                            emailRedirectTo: window.location.origin
                        }
                    });
                    
                    if (error3) {
                        log(`❌ Todas as tentativas falharam`);
                        statusDiv.innerHTML = `<div class="status error">❌ Erro em todas as tentativas:<br>1. ${error.message}<br>2. ${error2.message}<br>3. ${error3.message}</div>`;
                        return;
                    } else {
                        log(`✅ Sucesso na tentativa 3: ${JSON.stringify(data3.user, null, 2)}`);
                        statusDiv.innerHTML = `<div class="status success">✅ Usuário criado (tentativa 3)!<br>ID: ${data3.user?.id}<br>Email: ${data3.user?.email}<br>Confirmação: ${data3.user?.email_confirmed_at ? 'Sim' : 'Pendente'}</div>`;
                        
                        document.getElementById('login-email').value = email;
                        document.getElementById('login-password').value = password;
                        return;
                    }
                } else {
                    log(`✅ Sucesso na tentativa 2: ${JSON.stringify(data2.user, null, 2)}`);
                    statusDiv.innerHTML = `<div class="status success">✅ Usuário criado (tentativa 2)!<br>ID: ${data2.user?.id}<br>Email: ${data2.user?.email}<br>Confirmação: ${data2.user?.email_confirmed_at ? 'Sim' : 'Pendente'}</div>`;
                    
                    document.getElementById('login-email').value = email;
                    document.getElementById('login-password').value = password;
                    return;
                }
            }

            log(`✅ Usuário criado na primeira tentativa: ${JSON.stringify(data.user, null, 2)}`);
            statusDiv.innerHTML = `<div class="status success">✅ Usuário criado!<br>ID: ${data.user?.id}<br>Email: ${data.user?.email}<br>Confirmação: ${data.user?.email_confirmed_at ? 'Sim' : 'Pendente'}</div>`;
            
            // Auto-preencher campos de login
            document.getElementById('login-email').value = email;
            document.getElementById('login-password').value = password;

        } catch (error) {
            log(`❌ Exceção SignUp: ${error.message}`);
            statusDiv.innerHTML = `<div class="status error">❌ Exceção: ${error.message}</div>`;
        }
    };

    window.testSignIn = async function() {
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const statusDiv = document.getElementById('signin-status');

        try {
            statusDiv.innerHTML = '<div class="status">🔄 Fazendo login...</div>';
            log(`Tentando login: ${email}`);

            const { data, error } = await supabase.auth.signInWithPassword({
                email: email,
                password: password
            });

            if (error) {
                log(`❌ Erro SignIn: ${error.message}`);
                statusDiv.innerHTML = `<div class="status error">❌ Erro: ${error.message}</div>`;
                return;
            }

            log(`✅ Login realizado: ${JSON.stringify(data.user, null, 2)}`);
            statusDiv.innerHTML = `<div class="status success">✅ Login realizado!<br>ID: ${data.user.id}<br>Token: ${data.session.access_token.substring(0, 20)}...</div>`;

        } catch (error) {
            log(`❌ Exceção SignIn: ${error.message}`);
            statusDiv.innerHTML = `<div class="status error">❌ Exceção: ${error.message}</div>`;
        }
    };

    window.checkSession = async function() {
        const statusDiv = document.getElementById('session-status');

        try {
            const { data: { session } } = await supabase.auth.getSession();
            
            if (session) {
                log(`✅ Sessão ativa: ${session.user.email}`);
                statusDiv.innerHTML = `<div class="status success">✅ Sessão ativa<br>Usuário: ${session.user.email}<br>Expira: ${new Date(session.expires_at * 1000).toLocaleString()}</div>`;
            } else {
                log(`❌ Nenhuma sessão ativa`);
                statusDiv.innerHTML = '<div class="status error">❌ Nenhuma sessão ativa</div>';
            }
        } catch (error) {
            log(`❌ Erro ao verificar sessão: ${error.message}`);
            statusDiv.innerHTML = `<div class="status error">❌ Erro: ${error.message}</div>`;
        }
    };

    window.logout = async function() {
        const statusDiv = document.getElementById('session-status');
        
        try {
            await supabase.auth.signOut();
            log(`✅ Logout realizado`);
            statusDiv.innerHTML = '<div class="status success">✅ Logout realizado</div>';
        } catch (error) {
            log(`❌ Erro no logout: ${error.message}`);
            statusDiv.innerHTML = `<div class="status error">❌ Erro: ${error.message}</div>`;
        }
    };

    window.clearLog = function() {
        document.getElementById('log').textContent = '';
    };

    // Auto-executar teste de conexão
    log('Página carregada. Cliente Supabase disponível.');
    </script>

    <p style="text-align: center; margin-top: 30px;">
        <a href="/create-user">← Voltar para o formulário</a> | 
        <a href="/login">Login</a>
    </p>
</body>
</html>