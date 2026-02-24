<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
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
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 { text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px; border: 1px solid #ddd;
            border-radius: 4px; box-sizing: border-box; font-size: 14px;
        }
        button {
            width: 100%; padding: 12px; background-color: #007bff; color: white;
            border: none; border-radius: 4px; cursor: pointer; font-size: 16px;
        }
        button:hover { background-color: #0056b3; }
        button:disabled { background-color: #ccc; cursor: not-allowed; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .links { text-align: center; margin-top: 20px; }
        .links a { color: #007bff; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <div id="error-message" class="alert alert-error" style="display: none;"></div>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="form-group">
            <label for="email">Email:</label>
            <input id="email" type="email" placeholder="email@exemplo.com" required>
        </div>

        <div class="form-group">
            <label for="password">Senha:</label>
            <input id="password" type="password" placeholder="A sua senha" required>
        </div>

        <button id="login-btn" onclick="login()">Entrar</button>

        <div class="links">
            <p><a href="/create-user">Criar nova conta</a></p>
        </div>
    </div>

    <script type="module">
        import { createClient } from 'https://esm.sh/@supabase/supabase-js';

        const supabase = createClient(
            '{{ config('services.supabase.url') }}',
            '{{ config('services.supabase.anon_key') }}',
            { auth: { persistSession: true } }
        );

        async function login() {
            const btn = document.getElementById('login-btn');
            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';
            btn.disabled = true;
            btn.textContent = 'A entrar...';

            try {
                // 1. Autenticar no Supabase
                const { data, error } = await supabase.auth.signInWithPassword({
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value
                });

                if (error) {
                    errorDiv.textContent = error.message;
                    errorDiv.style.display = 'block';
                    return;
                }

                const token = data.session.access_token;

                // 2. Enviar token ao Laravel para fazer login na sessão
                const response = await fetch('{{ route('login.submit') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ token: token })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    errorDiv.textContent = result.message || 'Erro ao iniciar sessão';
                    errorDiv.style.display = 'block';
                    return;
                }

                // 3. Redirecionar para o dashboard
                window.location.href = result.redirect || '/dashboard';

            } catch (err) {
                errorDiv.textContent = 'Erro inesperado: ' + err.message;
                errorDiv.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.textContent = 'Entrar';
            }
        }

        // Enter key para submeter
        document.getElementById('password').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') login();
        });
        document.getElementById('email').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') login();
        });

        window.login = login;
    </script>
</body>

</html>
