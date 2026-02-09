<?php
// Configuración de la Base de Datos con soporte para variables de entorno (Railway, Docker, etc.)
// Si la variable de entorno no existe, usa el valor por defecto (Localhost)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'lastorre_gym');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');
?>