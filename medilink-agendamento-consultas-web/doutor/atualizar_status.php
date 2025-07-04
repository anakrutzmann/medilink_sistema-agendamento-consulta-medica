<?php
session_start();
require_once '../connection/connection_sqlite.php';

// Verifica se o médico está logado
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'doctor') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($appointment_id && in_array($action, ['aceitar', 'rejeitar'])) {
        $status = $action === 'aceitar' ? 'confirmado' : 'rejeitado';

        $stmt = $database->prepare("UPDATE appointment SET status = ? WHERE id = ?");
        $stmt->execute([$status, $appointment_id]);
    }
}

header('Location: dashboard.php');
exit;
