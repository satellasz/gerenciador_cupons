<?php
session_start();
require_once __DIR__ . '/../db/db_connection.php';
require_once __DIR__ . '/../controllers/CriarContaController.php';

$controller = new CriarContaController($conn);
$controller->criarConta();
