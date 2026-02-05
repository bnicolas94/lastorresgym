<?php
require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = Database::getInstancia();

$sql = "CREATE TABLE IF NOT EXISTS arqueos_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora_cierre DATETIME DEFAULT CURRENT_TIMESTAMP,
    ingresos_sistema DECIMAL(10,2) NOT NULL DEFAULT 0,
    retiros_sistema DECIMAL(10,2) NOT NULL DEFAULT 0,
    saldo_sistema DECIMAL(10,2) NOT NULL DEFAULT 0,
    efectivo_real DECIMAL(10,2) NOT NULL DEFAULT 0,
    diferencia DECIMAL(10,2) NOT NULL DEFAULT 0,
    usuario_id INT NOT NULL,
    observaciones TEXT NULL,
    FOREIGN KEY (usuario_id) REFERENCES alumnos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($db->query($sql)) {
    echo "Table 'arqueos_caja' created successfully.<br>";
} else {
    echo "Error creating table: " . $db->getConexion()->error . "<br>";
}
?>