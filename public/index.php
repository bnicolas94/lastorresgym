<?php
// Iniciar sesión
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar Config
require_once '../app/config/config.php';

// Cargar Core
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';
require_once '../app/core/View.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Router.php';

// Iniciar Router
$app = new Router();
?>