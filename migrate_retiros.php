<?php
// migrate_retiros.php
require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = Database::getInstancia();

$db->query("DROP TABLE IF EXISTS retiros_caja");

$sql = "CREATE TABLE IF NOT EXISTS retiros_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    fecha_retiro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES alumnos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($db->query($sql)) {
    echo "Table 'retiros_caja' created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . $db->getConexion()->error . "<br>";
}
?>