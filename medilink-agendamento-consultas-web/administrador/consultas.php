<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../connection/connection_sqlite.php';

// Buscar todas as consultas
$stmt = $database->query("
    SELECT a.*, 
           p.name AS patient_name, 
           d.name AS doctor_name
    FROM appointment a
    JOIN patient p ON a.patient_id = p.id
    JOIN doctor d ON a.doctor_id = d.id
    ORDER BY a.date DESC, a.time DESC
");
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consultas - Administrador - MediLink</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #eef5fb;
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        h2 {
            color: #0077cc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #0077cc;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
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

<h2>Consultas Realizadas e Agendadas</h2>

<?php if (empty($consultas)): ?>
    <p>Nenhuma consulta registrada.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Status</th>
                <th>Motivo Cancelamento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($consultas as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['patient_name']) ?></td>
                    <td><?= htmlspecialchars($c['doctor_name']) ?></td>
                    <td><?= htmlspecialchars($c['date']) ?></td>
                    <td><?= htmlspecialchars($c['time']) ?></td>
                    <td><?= ucfirst($c['status']) ?></td>
                    <td><?= htmlspecialchars($c['cancel_reason'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
