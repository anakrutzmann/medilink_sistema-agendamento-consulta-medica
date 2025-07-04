<?php
require_once 'connection/connection_sqlite.php';

try {
    echo "<!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Visualizar Tabelas</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f2f2f2;
                padding: 20px;
                color: #333;
            }
            h2, h3 {
                color: #005f99;
            }
            ul {
                background: #fff;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                max-width: 600px;
            }
            li {
                margin: 8px 0;
            }
        </style>
    </head>
    <body>
    ";

    echo "<h2>Tabelas existentes no banco de dados:</h2><ul>";

    // Listar todas as tabelas
    $tables = $database->query("SELECT name FROM sqlite_master WHERE type='table'");
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table['name']) . "</li>";
    }
    echo "</ul>";

    // Estrutura da tabela 'patient'
    echo "<h3>Estrutura da tabela 'patient':</h3><ul>";
    $columns = $database->query("PRAGMA table_info(patient)");
    foreach ($columns as $col) {
        echo "<li><strong>" . htmlspecialchars($col['name']) . "</strong> (" . htmlspecialchars($col['type']) . ")</li>";
    }
    echo "</ul>";

    echo "</body></html>";

} catch (PDOException $e) {
    echo "Erro ao acessar banco de dados: " . $e->getMessage();
}
?>
