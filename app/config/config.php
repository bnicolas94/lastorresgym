<?php
// Detección automática del protocolo (http o https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Detección automática de la carpeta base (independiente de si es /v4/public o raíz)
// dirname($_SERVER['SCRIPT_NAME']) devuelve algo como "/lastorresgym/v4/public"
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

define('BASE_URL', $protocol . "://" . $host . $path . "/");

// Zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Nombre de la App
define('APP_NAME', 'Las Torres Gym');
?>