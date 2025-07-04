<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>MediLink - Sistema de Agendamento Médico</title>
  <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: #fff;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    h1 {
        font-size: 3em;
        margin-bottom: 0.3em;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    p {
        font-size: 1.2em;
        margin-bottom: 2em;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
    }
    a.button {
        display: inline-block;
        background: #fff;
        color: #2575fc;
        padding: 15px 30px;
        border-radius: 30px;
        font-weight: bold;
        text-decoration: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: background 0.3s ease, color 0.3s ease;
    }
    a.button:hover {
        background: #2575fc;
        color: #fff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    footer {
        position: absolute;
        bottom: 20px;
        font-size: 0.9em;
        color: rgba(255,255,255,0.7);
    }
  </style>
</head>
<body>

<h1>MediLink</h1>
<p>Agende sua consulta médica de forma rápida e fácil.</p>
<a href="login.php" class="button">Fazer Login</a>

<footer>© 2025 MediLink - Desenvolvido por Ana</footer>

</body>
</html>
