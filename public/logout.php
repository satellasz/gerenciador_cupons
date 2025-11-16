<?php
session_start();

// limpa todas as variáveis da sessão
$_SESSION = [];
session_destroy();

// manda para a home de login
header("Location: index.php");
exit;
