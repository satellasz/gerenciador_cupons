<?php
session_start();
require_once __DIR__ . '/../../db/db_connection.php';
require_once __DIR__ . '/../../controllers/ComercianteController.php';

$controller = new ComercianteController($conn);
$controller->validarCupom();
