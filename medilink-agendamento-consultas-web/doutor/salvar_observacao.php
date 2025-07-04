<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['usertype'] !== 'doctor') {
    header("Location: ../login.php");
    exit;
}

require_once '../connection/connection_sqlite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? null;
    $observation = $_POST['observation'] ?? '';

    if (!$appointment_id) {
        echo "ID da consulta inválido.";
        exit;
    }

    // Verificar se a consulta pertence ao médico logado
    $stmt = $database->prepare("
        SELECT a.id FROM appointment a
        JOIN doctor d ON a.doctor_id = d.id
        WHERE a.id = ? AND d.email = ?
    ");
    $stmt->execute([$appointment_id, $_SESSION['email']]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        echo "Consulta não encontrada ou acesso negado.";
        exit;
    }

    // Atualizar a observação (prontuário)
    $stmt = $database->prepare("UPDATE appointment SET observation = ? WHERE id = ?");
    $stmt->execute([$observation, $appointment_id]);

    // Redirecionar com mensagem de sucesso (pode usar GET para indicar sucesso)
    header("Location: dashboard.php?msg=Prontuário salvo com sucesso");
    exit;
} else {
    echo "Método inválido.";
}
