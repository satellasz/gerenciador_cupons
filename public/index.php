<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
require_once '../db/db_connection.php';
require_once '../controllers/LoginController.php';

$controller = new LoginController($conn);
$controller->login();
