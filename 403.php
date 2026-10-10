<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Acesso não autorizado | Barra Space Center</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, sans-serif;
            color: #1a1f2e;
            background: #fcfaf7;
        }

        * { box-sizing: border-box; }

        body {
            display: grid;
            min-height: 100vh;
            min-height: 100svh;
            margin: 0;
            padding: 24px;
            place-items: center;
        }

        main {
            width: min(100%, 520px);
            padding: clamp(28px, 7vw, 56px);
            border: 1px solid rgba(184, 134, 11, .2);
            border-radius: 20px;
            background: #fff;
            text-align: center;
            box-shadow: 0 20px 60px rgba(10, 16, 32, .08);
        }

        .code {
            margin: 0;
            color: #b8860b;
            font-size: clamp(3.5rem, 15vw, 5.5rem);
            font-weight: 700;
            line-height: 1;
        }

        h1 {
            margin: 20px 0 12px;
            color: #0a1020;
            font-size: clamp(1.5rem, 6vw, 2rem);
        }

        main > p:not(.code) {
            margin: 0;
            color: #6b7280;
            line-height: 1.7;
        }

        a {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            margin-top: 28px;
            padding: 12px 22px;
            border-radius: 999px;
            background: #0a1020;
            color: #fff;
            font-size: .95rem;
            font-weight: 600;
            text-decoration: none;
        }

        a:hover, a:focus-visible { background: #b8860b; }
        a:focus-visible { outline: 3px solid #d4a94b; outline-offset: 3px; }
    </style>
</head>
<body>
    <main aria-labelledby="error-title">
        <p class="code" aria-hidden="true">403</p>
        <h1 id="error-title">Acesso não autorizado</h1>
        <p>Sua conta não tem permissão para acessar esta página. Se você acredita que deveria ter acesso, fale com a administração.</p>
        <a href="index.php">Voltar ao site</a>
    </main>
</body>
</html>
