<?php
// debug_access.php
$dbName = __DIR__ . "\\controlpersonal.mdb";
if (!file_exists($dbName)) {
    die("File not found: $dbName\n");
}

// Try generic ODBC driver
$dsn = "odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$dbName";
// Alternative if the above fails (sometimes purely *.mdb)
// $dsn = "odbc:DRIVER={Microsoft Access Driver (*.mdb)};Dbq=$dbName";

try {
    $conn = new PDO($dsn);
    echo "Connected successfully to Access DB.\n";

    // List Tables (ODBC specific)
    // SQLTables is a standard ODBC function but via PDO we query schema
    $stmt = $conn->query("SELECT Name FROM MSysObjects WHERE Type=1 AND Flags=0");
    // Type=1 is local table, Flags=0 usually user tables.

    if ($stmt) {
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            echo "Table: $table\n";
            // Try to get columns
            // Simple select top 1 to get column metadata
            try {
                $q = $conn->query("SELECT TOP 1 * FROM [$table]");
                if ($q) {
                    $colCount = $q->columnCount();
                    for ($i = 0; $i < $colCount; $i++) {
                        $meta = $q->getColumnMeta($i);
                        echo "   - " . $meta['name'] . "\n";
                    }
                }
            } catch (Exception $e) {
                echo "   (Could not read columns)\n";
            }
        }
    } else {
        echo "Could not query MSysObjects (Permission might be denied).\n";
        // Fallback: Try known table names if MSysObjects is hidden
        $fallbackTables = ['Pagos', 'Cuotas', 'Socios', 'Alumnos', 'Movimientos', 'Caja', 'Ingresos', 'Ventas', 'Facturas', 'Cobros', 'Clientes'];
        foreach ($fallbackTables as $t) {
            try {
                $q = $conn->query("SELECT TOP 1 * FROM [$t]");
                if ($q) {
                    echo "Found Table: $t\n";
                    $colCount = $q->columnCount();
                    for ($i = 0; $i < $colCount; $i++) {
                        $meta = $q->getColumnMeta($i);
                        echo "   - " . $meta['name'] . "\n";
                    }
                }
            } catch (Exception $e) { /* ignore */
            }
        }
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    // Check available drivers
    echo "Available Drivers: " . implode(", ", PDO::getAvailableDrivers()) . "\n";
}
