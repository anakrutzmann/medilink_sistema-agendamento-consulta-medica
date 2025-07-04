<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../connection/connection_sqlite.php';

$mensagem = "";

// Adicionar médico
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $specialty_id = $_POST['specialty_id'] ?? '';

    if ($name && $email && $password && $specialty_id) {
        try {
            // Inserir na tabela doctor
            $stmt1 = $database->prepare("INSERT INTO doctor (name, email, specialty_id) VALUES (?, ?, ?)");
            $stmt1->execute([$name, $email, $specialty_id]);

            // Inserir na tabela webuser
            $stmt2 = $database->prepare("INSERT INTO webuser (email, password, usertype) VALUES (?, ?, 'doctor')");
            $stmt2->execute([$email, $password]);

            $mensagem = "✅ Médico cadastrado com sucesso!";
        } catch (PDOException $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    } else {
        $mensagem = "⚠️ Preencha todos os campos.";
    }
}

// Buscar especialidades
$especialidades = $database->query("SELECT id, name FROM specialties")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Médicos - MediLink</title>
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
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
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

<h2>Cadastrar Novo Médico</h2>

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

    <label for="specialty_id">Especialidade:</label>
    <select name="specialty_id" id="specialty_id" required>
        <option value="">Selecione</option>
        <?php foreach ($especialidades as $esp): ?>
            <option value="<?= $esp['id'] ?>"><?= htmlspecialchars($esp['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Cadastrar Médico</button>
</form>

</body>
</html>
