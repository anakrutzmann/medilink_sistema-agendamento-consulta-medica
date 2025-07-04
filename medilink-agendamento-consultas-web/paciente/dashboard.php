<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'patient') {
    header('Location: ../login.php');
    exit;
}

// Conexão com o banco
require_once '../connection/connection_sqlite.php';

// Buscar dados do paciente pelo email
$stmt = $database->prepare("SELECT * FROM patient WHERE email = ?");
$stmt->execute([$_SESSION['email']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - MediLink</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            margin: 0;
            padding: 0 20px;
            min-height: 100vh;
        }
        header {
            display: flex;
            justify-content: flex-end;
            padding: 20px 0;
        }
        header form button {
            background-color: #ff4b5c;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        header form button:hover {
            background-color: #e63946;
        }
        main {
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            margin-top: 0;
            font-weight: 700;
        }
        .links {
            margin-top: 30px;
            display: flex;
            gap: 20px;
        }
        .links a {
            background-color: #ff4b5c;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .links a:hover {
            background-color: #e63946;
        }
    </style>
</head>
<body>

<header>
    <form action="../logout.php" method="POST" style="margin:0;">
        <button type="submit">Sair</button>
    </form>
</header>

<main>
    <h1>Bem-vindo, <?= htmlspecialchars($patient['name'] ?? $_SESSION['email']) ?>!</h1>
    <p>Área do paciente no sistema MediLink.</p>

    <div class="links">
        <a href="schedule_appointment.php">Agendar Consulta</a>
        <a href="consultas_agendadas.php">Minhas Consultas</a>
    </div>
</main>

</body>
</html>
