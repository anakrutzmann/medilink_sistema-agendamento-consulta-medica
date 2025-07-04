<?php
session_start();
require_once 'connection/connection_sqlite.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    if (!$name || !$email || !$password || !$password_confirm) {
        $error = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "E-mail inválido.";
    } elseif ($password !== $password_confirm) {
        $error = "As senhas não conferem.";
    } else {
        // Verificar se email já existe em webuser
        $stmt = $database->prepare("SELECT id FROM webuser WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Este e-mail já está cadastrado.";
        } else {
            // Inserir paciente na tabela patient
            $stmt = $database->prepare("INSERT INTO patient (name, email) VALUES (?, ?)");
            $stmt->execute([$name, $email]);

            // Inserir usuário na tabela webuser (com tipo patient)
            // Atenção: senha salva em texto puro aqui para simplicidade, em produção use hash!
            $stmt = $database->prepare("INSERT INTO webuser (email, password, usertype) VALUES (?, ?, 'patient')");
            $stmt->execute([$email, $password]);

            $success = "Cadastro realizado com sucesso! Você já pode fazer login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<title>Cadastrar Paciente - MediLink</title>
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
  .register-box {
    background: rgba(255,255,255,0.1);
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    width: 360px;
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
  input[type="text"],
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
    background-color: #28a745;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #1e7e34;
  }
  .error-message {
    margin-top: 12px;
    color: #ffcccc;
    background: #990000;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
  }
  .success-message {
    margin-top: 12px;
    color: #d4edda;
    background: #155724;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
  }
  a.back-login {
    margin-top: 20px;
    display: inline-block;
    color: #aaddaa;
    text-decoration: underline;
    cursor: pointer;
  }
</style>
</head>
<body>

<div class="register-box">
  <h2>Criar Conta - Paciente</h2>

  <?php if ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="success-message"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if (!$success): ?>
    <form method="POST" action="register.php">
      <label for="name">Nome Completo</label>
      <input type="text" name="name" id="name" required />

      <label for="email">E-mail</label>
      <input type="email" name="email" id="email" required />

      <label for="password">Senha</label>
      <input type="password" name="password" id="password" required />

      <label for="password_confirm">Confirme a Senha</label>
      <input type="password" name="password_confirm" id="password_confirm" required />

      <button type="submit">Cadastrar</button>
    </form>
  <?php endif; ?>

  <a href="login.php" class="back-login">Voltar para Login</a>
</div>

</body>
</html>
