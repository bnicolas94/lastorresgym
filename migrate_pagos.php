<?php
require_once 'app/config/database.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Add metodo_pago
$sql1 = "ALTER TABLE pagos ADD COLUMN metodo_pago VARCHAR(50) DEFAULT 'mercadopago' AFTER monto";
if ($conn->query($sql1) === TRUE) {
    echo "Column 'metodo_pago' added successfully.\n";
} else {
    echo "Error adding 'metodo_pago': " . $conn->error . "\n";
}

// 2. Add observaciones
$sql2 = "ALTER TABLE pagos ADD COLUMN observaciones TEXT NULL AFTER estado";
if ($conn->query($sql2) === TRUE) {
    echo "Column 'observaciones' added successfully.\n";
} else {
    echo "Error adding 'observaciones': " . $conn->error . "\n";
}

$conn->close();
?>