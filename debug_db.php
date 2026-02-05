<?php
require_once 'app/config/database.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$res = $conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) {
    echo $row[0] . "\n";
    if (stripos($row[0], 'pago') !== false || stripos($row[0], 'venta') !== false || stripos($row[0], 'movimiento') !== false) {
        $cols = $conn->query("DESCRIBE " . $row[0]);
        while ($c = $cols->fetch_assoc()) {
            echo "   - " . $c['Field'] . " (" . $c['Type'] . ")\n";
        }
    }
}
