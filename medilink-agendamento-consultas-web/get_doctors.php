<?php
require_once 'connection/connection_sqlite.php';

if (isset($_GET['specialty_id'])) {
    $specialty_id = intval($_GET['specialty_id']);
    $stmt = $database->prepare("SELECT id, name FROM doctor WHERE specialty_id = ?");
    $stmt->execute([$specialty_id]);
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($doctors);
    exit;
}

// Caso não tenha o parâmetro specialty_id
echo json_encode([]);
?>
