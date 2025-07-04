<?php
session_start();       // Inicia a sessão
session_unset();       // Limpa todas as variáveis de sessão
session_destroy();     // Destroi a sessão

// Redireciona para a página inicial (home)
header('Location: index.php');
exit;
