<?php
require_once __DIR__ . '/../config/database.php';

class Database
{
    private static $instancia = null;
    private $conn;

    private function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Error de conexión a la base de datos: " . $this->conn->connect_error);
        }

        $this->conn->set_charset(DB_CHARSET);
    }

    public static function getInstancia()
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    public function getConexion()
    {
        return $this->conn;
    }

    // Método helper para queries simples
    public function query($sql)
    {
        return $this->conn->query($sql);
    }

    // Prevent cloning
    private function __clone()
    {
    }
}
?>