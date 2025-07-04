<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../connection/connection_sqlite.php';

// Adicionar nova especialidade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $stmt = $database->prepare("INSERT INTO specialties (name) VALUES (?)");
        $stmt->execute([$name]);
        header("Location: especialidades.php");
        exit;
    }
}

// Excluir especialidade
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    // Opcional: checar se algum médico está vinculado antes de excluir
    $stmt = $database->prepare("DELETE FROM specialties WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: especialidades.php");
    exit;
}

// Buscar especialidades
$stmt = $database->query("SELECT * FROM specialties ORDER BY name");
$specialties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Especialidades - MediLink</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<h2>Especialidades Médicas</h2>

<form method="POST" style="margin-bottom: 20px;">
    <input type="text" name="name" placeholder="Nova especialidade" required />
    <button type="submit">Adicionar</button>
</form>

<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($specialties as $s): ?>
        <tr>
            <td><?= $s['id'] ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td>
                <a href="editar_especialidade.php?id=<?= $s['id'] ?>">Editar</a> |
                <a href="especialidades.php?delete_id=<?= $s['id'] ?>" onclick="return confirm('Excluir especialidade?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p><a href="dashboard.php">Voltar ao Dashboard</a></p>
</body>
</html>
