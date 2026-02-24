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
        input[type="email"], input[type="password"], input[type="text"], input[type="number"], select {
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
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="{{ old('nome') }}" placeholder="Nome completo do aluno" required>
            @error('nome')
                <div class="error">{{ $message }}</div>
            @enderror
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

        <div class="form-group">
            <label for="escola_instituicao">Escola / Instituição:</label>
            <input type="text" id="escola_instituicao" name="escola_instituicao" value="{{ old('escola_instituicao') }}" placeholder="Nome da escola ou instituição" required>
            @error('escola_instituicao')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="ano_escolaridade">Ano de Escolaridade:</label>
            <select id="ano_escolaridade" name="ano_escolaridade" required>
                <option value="">-- Selecionar --</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ old('ano_escolaridade') == $i ? 'selected' : '' }}>{{ $i }}º ano</option>
                @endfor
            </select>
            @error('ano_escolaridade')
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

    const supabase = createClient('{{ config('services.supabase.url') }}', '{{ config('services.supabase.anon_key') }}');

    async function createUser() {
        const form = document.querySelector('form');
        const nome = document.getElementById('nome').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const escola_instituicao = document.getElementById('escola_instituicao').value.trim();
        const ano_escolaridade = document.getElementById('ano_escolaridade').value;

        if (!nome || !email || !password || !escola_instituicao || !ano_escolaridade) {
            alert('Por favor, preencha todos os campos obrigatórios');
            return;
        }

        try {
            // 1. Criar utilizador no Supabase Auth
            const { data, error } = await supabase.auth.signUp({
                email: email,
                password: password,
                options: {
                    data: {
                        nome: nome,
                        escola_instituicao: escola_instituicao,
                        ano_escolaridade: parseInt(ano_escolaridade)
                    }
                }
            });

            if (error) {
                console.error('Erro ao criar utilizador:', error);
                alert('Erro ao criar utilizador: ' + error.message);
                return;
            }

            if (!data.user) {
                alert('Erro: utilizador não foi criado');
                return;
            }

            console.log('Utilizador auth criado:', data.user.id);

            // 2. Inserir dados na tabela alunos
            const { data: alunoData, error: alunoError } = await supabase
                .from('alunos')
                .insert({
                    id: data.user.id,
                    nome: nome,
                    email: email,
                    escola_instituicao: escola_instituicao,
                    ano_escolaridade: parseInt(ano_escolaridade)
                })
                .select();

            if (alunoError) {
                console.error('Erro ao inserir aluno:', alunoError);
                alert('⚠️ Utilizador auth criado, mas erro ao guardar dados do aluno: ' + alunoError.message);
                return;
            }

            console.log('Aluno inserido:', alunoData);
            alert('✅ Aluno criado com sucesso!\nNome: ' + nome + '\nEmail: ' + email + '\nEscola: ' + escola_instituicao + '\nAno: ' + ano_escolaridade + 'º');
            
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
