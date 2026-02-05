<?php
class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstancia()->getConexion();
    }

    // Método helper para escapar strings
    protected function escape($string)
    {
        return $this->db->real_escape_string($string);
    }
}
?>