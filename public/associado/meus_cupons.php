<?php
session_start();
require_once __DIR__ . '/../../db/db_connection.php';
require_once __DIR__ . '/../../controllers/AssociadoController.php';

$controller = new AssociadoController($conn);
$controller->meusCupons();
