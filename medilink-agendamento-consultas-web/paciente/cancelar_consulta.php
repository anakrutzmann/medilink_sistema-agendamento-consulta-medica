<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

require_once '../connection/connection_sqlite.php';

if (!isset($_GET['id'])) {
    die('ID da consulta não fornecido.');
}

$appointment_id = intval($_GET['id']);

// Buscar ID do paciente
$stmt = $database->prepare("SELECT id FROM patient WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die("Paciente não encontrado.");
}

// Verificar se a consulta pertence ao paciente
$stmt = $database->prepare("SELECT * FROM appointment WHERE id = ? AND patient_id = ?");
$stmt->execute([$appointment_id, $patient['id']]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    die("Consulta não encontrada ou não pertence a você.");
}

$mensagem = "";

// Verificar se a consulta está com mais de 24 horas antes do horário marcado
$data_hora_consulta = new DateTime($appointment['date'] . ' ' . $appointment['time']);
$agora = new DateTime();

$interval = $agora->diff($data_hora_consulta);
$horas_para_consulta = ($interval->days * 24) + $interval->h + ($interval->i / 60);

if ($horas_para_consulta < 24) {
    $mensagem = "⚠️ Cancelamento não permitido. A consulta deve ser cancelada com pelo menos 24 horas de antecedência.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($horas_para_consulta < 24) {
        // Bloqueia cancelamento
        $mensagem = "⚠️ Cancelamento não permitido. A consulta deve ser cancelada com pelo menos 24 horas de antecedência.";
    } else {
        $motivo = trim($_POST['motivo']);
        if (!empty($motivo)) {
            $stmt = $database->prepare("UPDATE appointment SET status = 'cancelado', cancel_reason = ? WHERE id = ?");
            $stmt->execute([$motivo, $appointment_id]);
            header("Location: consultas_agendadas.php");
            exit;
        } else {
            $mensagem = "⚠️ Digite o motivo do cancelamento.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Cancelar Consulta - MediLink</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        form {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
        }
        .aviso-importante {
            background: #ff4b5c;
            padding: 12px 15px;
            border-radius: 8px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(255,75,92,0.6);
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            font-size: 1.1em;
        }
        textarea {
            width: 100%;
            height: 110px;
            border-radius: 8px;
            border: none;
            padding: 12px;
            resize: none;
            font-size: 1em;
            box-shadow: inset 0 0 6px rgba(0,0,0,0.2);
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #ff4b5c;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1em;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
            box-shadow: 0 5px 15px rgba(255,75,92,0.6);
        }
        button:hover {
            background: #e63946;
            box-shadow: 0 8px 20px rgba(230,57,70,0.8);
        }
        button:disabled {
            background: #a54b51;
            cursor: not-allowed;
            box-shadow: none;
        }
        .mensagem {
            margin-top: 15px;
            font-weight: 600;
            color: #ffcccb;
            text-align: center;
        }
        a.voltar {
            display: inline-block;
            margin-top: 25px;
            color: #fff;
            text-decoration: underline;
            font-weight: 600;
            text-align: center;
        }
        a.voltar:hover {
            color: #ffb3b3;
        }
    </style>
</head>
<body>

<form method="POST" novalidate>
    <h2>Cancelar Consulta</h2>

    <div class="aviso-importante">
        ⚠️ Importante: A consulta só pode ser cancelada com pelo menos 24 horas de antecedência.
    </div>

    <label for="motivo">Informe o motivo do cancelamento:</label>
    <textarea name="motivo" id="motivo" <?= ($horas_para_consulta < 24) ? 'disabled' : 'required' ?>></textarea>

    <button type="submit" <?= ($horas_para_consulta < 24) ? 'disabled' : '' ?>>Confirmar Cancelamento</button>

    <?php if ($mensagem): ?>
        <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <a href="consultas_agendadas.php" class="voltar">Voltar para Consultas</a>
</form>

</body>
</html>
