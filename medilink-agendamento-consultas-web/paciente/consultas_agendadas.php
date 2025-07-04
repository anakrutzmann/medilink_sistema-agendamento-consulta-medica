<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

require_once '../connection/connection_sqlite.php';

// Pegar id do paciente pelo email da sessão
$stmt = $database->prepare("SELECT id FROM patient WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die("Paciente não encontrado.");
}

// Pegar consultas agendadas do paciente, com dados do médico e especialidade
$stmt = $database->prepare("
    SELECT a.id, a.date, a.time, a.status, a.cancel_reason,
           d.name AS doctor_name,
           s.name AS specialty_name
    FROM appointment a
    JOIN doctor d ON a.doctor_id = d.id
    JOIN specialties s ON d.specialty_id = s.id
    WHERE a.patient_id = ?
    ORDER BY a.date DESC, a.time DESC
");
$stmt->execute([$patient['id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<title>Consultas Agendadas - MediLink</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: #fff;
        margin: 0;
        padding: 40px 20px;
        min-height: 100vh;
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.4);
    }
    table {
        width: 100%;
        max-width: 900px;
        margin: 0 auto 40px;
        border-collapse: collapse;
        background: rgba(255,255,255,0.12);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    th, td {
        padding: 14px 18px;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    th {
        background: rgba(0,0,0,0.3);
    }
    tr:last-child td {
        border-bottom: none;
    }
    .status-agendado {
        color: #d4f1ff;
        font-weight: bold;
    }
    .status-cancelado {
        color: #ffb3b3;
        font-weight: bold;
    }
    .cancel-reason {
        display: block;
        font-size: 0.9em;
        color: #ffeaea;
        margin-top: 4px;
        font-style: italic;
    }
    .btn-back {
        display: block;
        width: 160px;
        margin: 0 auto;
        padding: 12px 0;
        background: #fff;
        color: #2575fc;
        font-weight: 700;
        text-align: center;
        border-radius: 30px;
        text-decoration: none;
        box-shadow: 0 7px 18px rgba(0,0,0,0.25);
        transition: background 0.3s ease, color 0.3s ease;
    }
    .btn-back:hover {
        background: #2575fc;
        color: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    }
    a.cancel-link {
        color: yellow;
        text-decoration: underline;
        cursor: pointer;
        font-weight: 600;
        margin-top: 6px;
        display: inline-block;
    }
</style>
</head>
<body>

<h2>Consultas Agendadas</h2>

<?php if (count($appointments) === 0): ?>
    <p style="text-align:center; font-size: 1.2em;">Nenhuma consulta agendada.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Data</th>
            <th>Horário</th>
            <th>Especialidade</th>
            <th>Médico</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $a): ?>
            <tr>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($a['date']))) ?></td>
                <td><?= htmlspecialchars($a['time']) ?></td>
                <td><?= htmlspecialchars($a['specialty_name']) ?></td>
                <td><?= htmlspecialchars($a['doctor_name']) ?></td>
                <td class="status-<?= strtolower($a['status']) ?>">
                    <?= htmlspecialchars(ucfirst($a['status'])) ?>
                    <?php if ($a['status'] === 'agendado'): ?>
                        <br>
                        <a class="cancel-link" href="cancelar_consulta.php?id=<?= $a['id'] ?>">Cancelar</a>
                    <?php elseif ($a['status'] === 'cancelado' && !empty($a['cancel_reason'])): ?>
                        <span class="cancel-reason">Motivo: <?= htmlspecialchars($a['cancel_reason']) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<a href="dashboard.php" class="btn-back">Voltar</a>

</body>
</html>
