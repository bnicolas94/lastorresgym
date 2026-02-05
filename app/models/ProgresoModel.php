<?php
class ProgresoModel extends Model
{

    public function obtenerEjerciciosDisponibles($alumnoId)
    {
        $sql = "SELECT DISTINCT nombre_categoria, categoria_id, nombre_ejercicio, ejercicio_id 
                FROM vw_progreso_fuerza 
                WHERE alumno_id = " . $this->escape($alumnoId) . " 
                ORDER BY nombre_categoria, nombre_ejercicio ASC";

        // Probamos ejecutar. Si la vista no existe, podría fallar aquí.
        // Pero asumimos que está.
        $res = $this->db->query($sql);

        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $cat = $row['nombre_categoria'];
                if (!isset($data[$cat])) {
                    $data[$cat] = [];
                }
                $data[$cat][] = [
                    'id' => $row['ejercicio_id'],
                    'nombre' => $row['nombre_ejercicio']
                ];
            }
        }
        return $data;
    }

    public function obtenerHistorialEjercicio($alumnoId, $ejercicioId)
    {
        $ejercicioId = $this->escape($ejercicioId);

        $sql = "SELECT fecha, rm_estimado, peso_maximo_real 
                FROM vw_progreso_fuerza 
                WHERE alumno_id = " . $this->escape($alumnoId) . " 
                  AND ejercicio_id = '$ejercicioId' 
                ORDER BY fecha ASC";

        $res = $this->db->query($sql);
        $historial = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $historial[] = [
                    'fecha' => date('d/m', strtotime($row['fecha'])), // Formato corto para gráfico
                    'fecha_full' => $row['fecha'],
                    'rm' => round((float) $row['rm_estimado'], 1),
                    'peso' => round((float) $row['peso_maximo_real'], 1)
                ];
            }
        }
        return $historial;
    }
}
?>