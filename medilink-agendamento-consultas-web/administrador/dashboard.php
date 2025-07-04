<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../connection/connection_sqlite.php';

// Buscar dados básicos para mostrar no dashboard
$total_patients = $database->query("SELECT COUNT(*) FROM patient")->fetchColumn();
$total_doctors = $database->query("SELECT COUNT(*) FROM doctor")->fetchColumn();
$total_appointments = $database->query("SELECT COUNT(*) FROM appointment")->fetchColumn();

$status_counts = $database->query("
    SELECT status, COUNT(*) AS count 
    FROM appointment 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Administrador - MediLink</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9f0f7; /* mesma cor da home */
            padding: 20px;
            color: #222;
        }
        h1, h2 {
            color: #005f99; /* cor primária da home */
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .logout {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .logout form button {
            background-color: #ff4b5c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .logout form button:hover {
            background-color: #e63946;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: white;
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-box h3 {
            margin-bottom: 10px;
            color: #0077cc;
        }
        .links {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .links a {
            display: inline-block;
            margin-right: 15px;
            padding: 10px 20px;
            background: #0077cc;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .links a:hover {
            background: #005fa3;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="logout">
        <form action="../logout.php" method="POST">
            <button type="submit">Sair</button>
        </form>
    </div>

    <h1>Bem-vindo, Administrador!</h1>

    <div class="stats">
        <div class="stat-box">
            <h3>Total de Pacientes</h3>
            <p><?= $total_patients ?></p>
        </div>
        <div class="stat-box">
            <h3>Total de Médicos</h3>
            <p><?= $total_doctors ?></p>
        </div>
        <div class="stat-box">
            <h3>Total de Consultas</h3>
            <p><?= $total_appointments ?></p>
        </div>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>Consultas Agendadas</h3>
            <p><?= $status_counts['agendado'] ?? 0 ?></p>
        </div>
        <div class="stat-box">
            <h3>Consultas Confirmadas</h3>
            <p><?= $status_counts['confirmado'] ?? 0 ?></p>
        </div>
        <div class="stat-box">
            <h3>Consultas Canceladas</h3>
            <p><?= $status_counts['cancelado'] ?? 0 ?></p>
        </div>
    </div>

    <div class="links">
        <a href="../administrador/pacientes.php">Gerenciar Pacientes</a>
        <a href="../administrador/medicos.php">Gerenciar Médicos</a>
        <a href="../administrador/consultas.php">Gerenciar Consultas</a>
    </div>

</div>
</body>
</html>
