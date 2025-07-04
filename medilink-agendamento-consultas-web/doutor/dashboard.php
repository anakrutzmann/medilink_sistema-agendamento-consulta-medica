<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'doctor') {
    header("Location: ../login.php");
    exit;
}

require_once '../connection/connection_sqlite.php';

// Buscar informações do médico logado
$stmt = $database->prepare("SELECT id, name FROM doctor WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    echo "Médico não encontrado.";
    exit;
}

$searchName = $_GET['search_name'] ?? '';

// Se tiver um nome para buscar, filtrar as consultas pelo paciente com nome parecido
if ($searchName) {
    $stmt = $database->prepare("
        SELECT a.*, p.name AS patient_name 
        FROM appointment a
        JOIN patient p ON a.patient_id = p.id
        WHERE a.doctor_id = ? AND p.name LIKE ?
        ORDER BY a.date DESC, a.time DESC
    ");
    $stmt->execute([$doctor['id'], "%$searchName%"]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Se não pesquisar, listar todas as consultas do médico ordenadas por data e hora
    $stmt = $database->prepare("
        SELECT a.*, p.name AS patient_name 
        FROM appointment a
        JOIN patient p ON a.patient_id = p.id
        WHERE a.doctor_id = ?
        ORDER BY a.date DESC, a.time DESC
    ");
    $stmt->execute([$doctor['id']]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Médico - MediLink</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        /* Seu estilo atual + ajustes para a busca */
        body {
            font-family: Arial, sans-serif;
            background-color: #e9f0f7;
            padding: 20px;
        }
        h2 {
            color: #005f99;
        }
        form.search-form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 250px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button.search-btn {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        button.search-btn:hover {
            background-color: #0056b3;
        }
        .consulta {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .consulta strong {
            color: #333;
        }
        textarea {
            width: 100%;
            margin-top: 8px;
            border-radius: 6px;
            padding: 8px;
            resize: vertical;
        }
        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            margin-top: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .action-btn.accept { background-color: #28a745; color: white; }
        .action-btn.reject { background-color: #dc3545; color: white; }
        .action-btn.save { background-color: #007bff; color: white; }
        .action-btn:hover { opacity: 0.9; }
        .logout {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .logout form button {
            background-color: #ff4b5c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .logout form button:hover {
            background-color: #e63946;
        }
    </style>
</head>
<body>

<div class="logout">
    <form action="../logout.php" method="POST">
        <button type="submit">Sair</button>
    </form>
</div>

<h2>Bem-vindo, <?= htmlspecialchars($doctor['name']) ?></h2>

<!-- Formulário de busca -->
<form class="search-form" method="GET" action="dashboard.php">
    <input type="text" name="search_name" placeholder="Buscar paciente pelo nome..." value="<?= htmlspecialchars($searchName) ?>" />
    <button type="submit" class="search-btn">Buscar</button>
    <?php if($searchName): ?>
        <a href="dashboard.php" style="margin-left:10px; text-decoration:none; color:#007bff;">Limpar busca</a>
    <?php endif; ?>
</form>

<?php if (empty($appointments)): ?>
    <p>Nenhuma consulta encontrada<?= $searchName ? " para \"$searchName\"" : "" ?>.</p>
<?php else: ?>
    <?php foreach ($appointments as $a): ?>
        <div class="consulta">
            <strong>Paciente:</strong> <?= htmlspecialchars($a['patient_name']) ?><br>
            <strong>Data:</strong> <?= htmlspecialchars($a['date']) ?><br>
            <strong>Hora:</strong> <?= htmlspecialchars($a['time']) ?><br>
            <strong>Status:</strong> <?= ucfirst($a['status']) ?><br>

            <?php if ($a['status'] === 'agendado'): ?>
                <form action="atualizar_status.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                    <button type="submit" name="action" value="aceitar" class="action-btn accept">Aceitar</button>
                    <button type="submit" name="action" value="rejeitar" class="action-btn reject">Rejeitar</button>
                </form>
            <?php elseif ($a['status'] === 'confirmado'): ?>
                <form action="salvar_observacao.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                    <label for="observation"><strong>Prontuário:</strong></label>
                    <textarea name="observation" rows="3" placeholder="Escreva aqui o prontuário do paciente..."><?= htmlspecialchars($a['observation'] ?? '') ?></textarea>
                    <button type="submit" class="action-btn save">Salvar Prontuário</button>
                </form>
            <?php endif; ?>

            <?php if (!empty($a['observation'])): ?>
                <div style="margin-top: 8px;">
                    <strong>Prontuário salvo:</strong><br>
                    <div style="background: #f1f1f1; padding: 8px; border-radius: 6px;"><?= nl2br(htmlspecialchars($a['observation'])) ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
