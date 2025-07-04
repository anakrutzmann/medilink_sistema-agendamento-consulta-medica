<?php
session_start();
require_once 'connection/connection_sqlite.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($password === $user['password']) {
                $_SESSION['email'] = $user['email'];
                $_SESSION['usertype'] = $user['usertype'];

                switch ($user['usertype']) {
                    case 'patient':
                        header('Location: paciente/dashboard.php');
                        break;
                    case 'doctor':
                        header('Location: doutor/dashboard.php');
                        break;
                    case 'admin':
                        header('Location: administrador/dashboard.php');
                        break;
                    default:
                        $error = 'Tipo de usuário inválido.';
                }
                exit;
            } else {
                $error = 'Senha incorreta.';
            }
        } else {
            $error = 'Usuário não encontrado.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<title>Login - MediLink</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-box {
        background: rgba(255,255,255,0.1);
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        width: 320px;
        text-align: center;
    }
    h2 {
        margin-bottom: 24px;
    }
    label {
        display: block;
        margin: 12px 0 6px;
        text-align: left;
    }
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 6px;
        outline: none;
    }
    button {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        background-color: #ff4b5c;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #e63946;
    }
    .error-message {
        margin-top: 12px;
        color: #ffcccc;
        background: #990000;
        padding: 10px;
        border-radius: 6px;
        text-align: center;
    }
    /* Botão criar conta */
    .btn-register {
        margin-top: 20px;
        display: inline-block;
        padding: 10px 25px;
        background-color: #28a745;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700;
        color: white;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }
    .btn-register:hover {
        background-color: #1e7e34;
    }
</style>
</head>
<body>

<div class="login-box">
    <h2>Login - MediLink</h2>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" required placeholder="seu@email.com" />

        <label for="password">Senha</label>
        <input type="password" name="password" id="password" required placeholder="Sua senha" />

        <button type="submit">Entrar</button>
    </form>

    <!-- Botão Criar Conta -->
    <a href="register.php" class="btn-register">Criar Conta</a>
</div>

</body>
</html>
