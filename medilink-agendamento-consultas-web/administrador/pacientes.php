<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../connection/connection_sqlite.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($name && $email && $password) {
        try {
            // Inserir na tabela patient
            $stmt1 = $database->prepare("INSERT INTO patient (name, email) VALUES (?, ?)");
            $stmt1->execute([$name, $email]);

            // Inserir na tabela webuser com tipo 'patient'
            $stmt2 = $database->prepare("INSERT INTO webuser (email, password, usertype) VALUES (?, ?, 'patient')");
            $stmt2->execute([$email, $password]);

            $mensagem = "✅ Paciente cadastrado com sucesso!";
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    } else {
        $mensagem = "⚠️ Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Paciente - MediLink</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef5fb;
            padding: 30px;
        }

        h2 {
            color: #0077cc;
        }

        form {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 500px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #0077cc;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #005fa3;
        }

        .mensagem {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }

        .voltar {
            margin-bottom: 20px;
        }

        .voltar a {
            background-color: #0077cc;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .voltar a:hover {
            background-color: #005fa3;
        }
    </style>
</head>
<body>

<div class="voltar">
    <a href="dashboard.php">← Voltar</a>
</div>

<h2>Adicionar Novo Paciente</h2>

<?php if ($mensagem): ?>
    <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
<?php endif; ?>

<form method="POST">
    <label for="name">Nome:</label>
    <input type="text" name="name" id="name" required>

    <label for="email">E-mail:</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Senha:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Cadastrar</button>
</form>

</body>
</html>
