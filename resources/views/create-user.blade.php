<!DOCTYPE html>
<html>
<head>
    <title>Criar Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .error {
            color: #721c24;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h2>Criar Novo Usuário</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            Erro: {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/create-user">
        @csrf
        
        <div class="form-group">
            <label for="name">Nome (opcional):</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nome do usuário">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@exemplo.com" required>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Criar Usuário</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
        <a href="/login">← Voltar para Login</a>
    </p>

    <script type="module">
    import { createClient } from 'https://esm.sh/@supabase/supabase-js';

    const supabase = createClient('{{ env('SUPABASE_URL') }}', '{{ env('SUPABASE_ANON_KEY') }}');

    async function createUser() {
        const form = document.querySelector('form');
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const name = document.getElementById('name').value;

        if (!email || !password) {
            alert('Por favor, preencha email e senha');
            return;
        }

        try {
            // Usar o método signUp diretamente
            const { data, error } = await supabase.auth.signUp({
                email: email,
                password: password,
                options: {
                    data: {
                        name: name || '',
                        created_by: 'frontend'
                    }
                }
            });

            if (error) {
                console.error('Erro:', error);
                alert('Erro ao criar usuário: ' + error.message);
                return;
            }

            console.log('Usuário criado:', data);
            alert('✅ Usuário criado com sucesso!\nID: ' + data.user.id + '\nEmail: ' + data.user.email);
            
            // Limpar formulário
            form.reset();
        } catch (err) {
            console.error('Exceção:', err);
            alert('❌ Erro inesperado: ' + err.message);
        }
    }

    // Adicionar evento ao formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        createUser();
    });

    // Também disponibilizar globalmente para teste
    window.createUser = createUser;
    </script>
</body>
</html>
