<?php
require_once 'connection/connection_sqlite.php';

try {
    // Verifica se coluna notified existe na tabela appointment
    $result = $database->query("PRAGMA table_info(appointment)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);

    $has_notified = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'notified') {
            $has_notified = true;
            break;
        }
    }

    if (!$has_notified) {
        $database->exec("ALTER TABLE appointment ADD COLUMN notified INTEGER DEFAULT 0");
        echo "✅ Coluna notified adicionada com sucesso!";
    } else {
        echo "⚠️ Coluna notified já existe, nada a fazer.";
    }
} catch (PDOException $e) {
    echo "⚠️ Erro ao adicionar coluna notified: " . $e->getMessage();
}
