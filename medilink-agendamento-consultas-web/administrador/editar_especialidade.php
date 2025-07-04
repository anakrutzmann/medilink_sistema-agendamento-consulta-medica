<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../connection/connection_sqlite.php';

if (!isset($_GET['id'])) {
    header("Location: especialidades.php");
    exit;
}

$id = intval($_GET['id']);

// Buscar especialidade
$stmt = $database->prepare("SELECT * FROM specialties WHERE id = ?");
$stmt->execute([$id]);
$specialty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$specialty) {
    echo "Especialidade não encontrada.";
    exit;
}

// Atualizar especialidade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $stmt = $database->prepare("UPDATE specialties SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        header("Location: especialidades.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Editar Especialidade - MediLink</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<h2>Editar Especialidade</h2>

<form method="POST">
    <input type="text" name="name" value="<?= htmlspecialchars($specialty['name']) ?>" required />
    <button type="submit">Salvar</button>
</form>

<p><a href="especialidades.php">Voltar</a></p>
</body>
</html>
