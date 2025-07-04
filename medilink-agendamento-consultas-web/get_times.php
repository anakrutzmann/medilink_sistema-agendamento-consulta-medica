<?php
require_once 'connection/connection_sqlite.php';

if (isset($_GET['doctor_id']) && isset($_GET['date'])) {
    $doctor_id = intval($_GET['doctor_id']);
    $date = $_GET['date'];

    // Verifica o dia da semana (1=Seg, 7=Dom)
    $dayOfWeek = date('N', strtotime($date));
    if ($dayOfWeek == 7) {
        // Domingo, sem horários
        echo json_encode([]);
        exit;
    }

    // Horários fixos (manhã e tarde)
    $morning = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00'];
    $afternoon = ['13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00'];

    $allTimes = array_merge($morning, $afternoon);

    // Busca horários já ocupados na tabela appointment para esse médico e data
    $stmt = $database->prepare("
        SELECT time FROM appointment 
        WHERE doctor_id = ? AND date = ? AND status = 'agendado'
    ");
    $stmt->execute([$doctor_id, $date]);
    $bookedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Filtra horários livres
    $availableTimes = array_values(array_diff($allTimes, $bookedTimes));

    header('Content-Type: application/json');
    echo json_encode($availableTimes);
    exit;
}

echo json_encode([]);
?>
