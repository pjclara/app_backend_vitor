<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>

    <h2>Login</h2>

    <input id="email" type="email" placeholder="Email">
    <input id="password" type="password" placeholder="Password">
    <button onclick="login()">Entrar</button>

    <script type="module">
        import {
            createClient
        } from 'https://esm.sh/@supabase/supabase-js';

        const supabase = createClient('{{ env('SUPABASE_URL') }}', '{{ env('SUPABASE_ANON_KEY') }}');

        async function login() {
            const {
                data,
                error
            } = await supabase.auth.signInWithPassword({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            });

            if (error) {
                alert(error.message);
                return;
            }

            console.log('Login successful:', data);

            const token = data.session.access_token;

            // Guardar token na sessão Laravel via POST
            const response = await fetch('/save-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    token: token,
                    user: data.user
                })
            });

            if (!response.ok) {
                alert('Erro ao guardar sessão. Tente novamente.');
                return;
            }

            window.location.href = "/dashboard";
        }

        // Make the function available globally
        window.login = login;
    </script>

</body>

</html>
