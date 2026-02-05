<?php

class AnunciosModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstancia();
    }

    /**
     * Obtiene todos los anuncios activos que no han expirado
     */
    public function obtenerActivos()
    {
        $sql = "SELECT * FROM anuncios 
                WHERE activo = 1 
                AND (fecha_expiracion IS NULL OR fecha_expiracion >= CURDATE())
                ORDER BY fecha_creacion DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene todos los anuncios (para el panel de administración)
     */
    public function obtenerTodos()
    {
        $sql = "SELECT * FROM anuncios ORDER BY fecha_creacion DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Guarda un nuevo anuncio o actualiza uno existente
     */
    public function guardar($data)
    {
        $titulo = $this->db->getConexion()->real_escape_string($data['titulo']);
        $contenido = $this->db->getConexion()->real_escape_string($data['contenido']);
        $prioridad = $this->db->getConexion()->real_escape_string($data['prioridad'] ?? 'baja');
        $imagen_url = isset($data['imagen_url']) ? $this->db->getConexion()->real_escape_string($data['imagen_url']) : null;
        $fecha_expiracion = !empty($data['fecha_expiracion']) ? "'" . $this->db->getConexion()->real_escape_string($data['fecha_expiracion']) . "'" : "NULL";

        if (isset($data['id']) && !empty($data['id'])) {
            $id = (int) $data['id'];
            $sql = "UPDATE anuncios SET 
                    titulo = '$titulo', 
                    contenido = '$contenido', 
                    prioridad = '$prioridad', 
                    imagen_url = " . ($imagen_url ? "'$imagen_url'" : "NULL") . ", 
                    fecha_expiracion = $fecha_expiracion
                    WHERE id = $id";
        } else {
            $sql = "INSERT INTO anuncios (titulo, contenido, prioridad, imagen_url, fecha_expiracion) 
                    VALUES ('$titulo', '$contenido', '$prioridad', " . ($imagen_url ? "'$imagen_url'" : "NULL") . ", $fecha_expiracion)";
        }

        return $this->db->query($sql);
    }

    /**
     * Elimina un anuncio (o lo marca como inactivo)
     */
    public function eliminar($id)
    {
        $id = (int) $id;
        $sql = "DELETE FROM anuncios WHERE id = $id";
        return $this->db->query($sql);
    }

    /**
     * Cambia el estado de activo de un anuncio
     */
    public function toggleActivo($id)
    {
        $id = (int) $id;
        $sql = "UPDATE anuncios SET activo = 1 - activo WHERE id = $id";
        return $this->db->query($sql);
    }
}
