<?php
require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = Database::getInstancia();

// Add 'destino' column if not exists
$sql = "ALTER TABLE pagos ADD COLUMN destino VARCHAR(50) NULL AFTER metodo_pago";

if ($db->query($sql)) {
    echo "Column 'destino' added successfully to 'pagos'.<br>";
} else {
    // Ignore error if column exists (Duplicate column name)
    if (strpos($db->getConexion()->error, 'Duplicate column name') !== false) {
        echo "Column 'destino' already exists.<br>";
    } else {
        echo "Error adding column: " . $db->getConexion()->error . "<br>";
    }
}
?>