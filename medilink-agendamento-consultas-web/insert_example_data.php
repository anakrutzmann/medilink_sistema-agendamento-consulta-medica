<?php
require_once "connection/connection_sqlite.php";

try {
    // Inserir especialidades
    $database->exec("
        INSERT INTO specialties (name) VALUES 
        ('Cardiologia'), 
        ('Clínico Geral');
    ");

    // Inserir médicos
    $database->exec("
        INSERT INTO doctor (name, email, specialty_id) VALUES 
        ('Dra. Beatriz Ramos', 'beatriz@clinic.com', 1),
        ('Dr. João Dorneles', 'joao@clinic.com', 2);
    ");

    // Inserir pacientes
    $database->exec("
        INSERT INTO patient (name, email) VALUES 
        ('Maria Silva', 'maria@email.com');
    ");

    // Inserir usuários para login
    $database->exec("
        INSERT INTO webuser (email, password, usertype) VALUES 
        ('admin@admin.com', 'admin123', 'admin'),
        ('beatriz@clinic.com', 'med123', 'doctor'),
        ('joao@clinic.com', 'med123', 'doctor'),
        ('maria@email.com', 'pac123', 'patient');
    ");

    // Inserir horários disponíveis (agenda)
    $database->exec("
        INSERT INTO schedule (doctor_id, date, time) VALUES 
        (1, '2025-07-05', '09:00'),
        (1, '2025-07-05', '10:00'),
        (2, '2025-07-05', '14:00'),
        (2, '2025-07-05', '15:00');
    ");

    echo "✅ Dados personalizados inseridos com sucesso!\n";
} catch (PDOException $e) {
    echo "Erro ao inserir dados: " . $e->getMessage();
}
