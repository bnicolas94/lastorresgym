<?php
class RutinasModel extends Model
{

    public function obtenerRutinaSemanal($alumnoId, $semana = null, $anio = null)
    {
        // Si no se especifica semana/año, intentamos con la actual
        if (!$semana || !$anio) {
            $currentYear = date('Y');
            $currentWeek = (int) date('W');

            // 1. Prioridad: Buscar si el alumno tiene rutina EXACTAMENTE en esta semana/año
            $sqlCurrent = "SELECT dyear, nombre_semana 
                           FROM rutinas 
                           WHERE alumno_id = " . $this->escape($alumnoId) . "
                             AND dyear = '$currentYear' 
                             AND CAST(nombre_semana AS UNSIGNED) = $currentWeek 
                           LIMIT 1";

            $res = $this->db->query($sqlCurrent);
            $current = $res ? $res->fetch_assoc() : null;

            if ($current) {
                $week = $current['nombre_semana'];
                $year = $current['dyear'];
            } else {
                // FALLBACK: Obtener la última rutina disponible del historial
                $sqlLast = "SELECT dyear, nombre_semana 
                            FROM rutinas 
                            WHERE alumno_id = " . $this->escape($alumnoId) . "
                            ORDER BY dyear DESC, CAST(nombre_semana AS UNSIGNED) DESC LIMIT 1";

                $res = $this->db->query($sqlLast);
                $last = $res ? $res->fetch_assoc() : null;

                if (!$last)
                    return null;

                $week = $last['nombre_semana'];
                $year = $last['dyear'];
            }
        } else {
            // Se especificó una semana y año concretos (Navegación Histórica)
            $week = $semana;
            $year = $anio;
        }

        // 2. Obtener los días de esa semana (la encontrada o la solicitada)
        $weekInt = (int) $week;

        $sql = "SELECT DISTINCT CAST(r.dia_semana AS UNSIGNED) as dia, 
                       COALESCE(NULLIF(c.nombre, ''), 'Varios') as categoria, c.id as categoria_id
                FROM rutinas r
                LEFT JOIN categorias_ejercicios c ON r.categoria_id = c.id
                WHERE r.alumno_id = $alumnoId
                  AND CAST(r.nombre_semana AS UNSIGNED) = $weekInt
                  AND r.dyear = '$year'
                ORDER BY dia ASC";

        $res = $this->db->query($sql);

        $rutina = [
            'semana' => $week,
            'anio' => $year,
            'dias' => []
        ];

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $dia = $row['dia'];
                if (!isset($rutina['dias'][$dia])) {
                    $rutina['dias'][$dia] = [];
                }
                $rutina['dias'][$dia][] = $row['categoria'];
            }
        }

        return $rutina;
    }

    public function obtenerEjerciciosPorDia($alumnoId, $semana, $anio, $dia)
    {
        // Obtenemos los ejercicios detallados para ese día específico
        // JOIN con ejercicios para obtener nombres, y series
        // LEFT JOIN con series_completadas (si la tuviéramos, pero en v3 parece que marcan fecha_fin en la misma tabla rutinas)

        // En v3 'rutinas' tiene: id, ejercicio_id, categoria_id, series, repeticiones, fecha_fin...

        $semana = (int) $semana;
        $anio = (int) $anio;
        $dia = (int) $dia;

        $sql = "SELECT r.id as rutina_id, r.observaciones as observacion, r.fecha_fin,
                       IFNULL(e.nombre, 'Ejercicio Desconocido') as ejercicio, e.img as ejercicio_img,
                       (SELECT COUNT(*) FROM series s WHERE s.rutina_id = r.id) as series,
                       (SELECT repeticiones FROM series s WHERE s.rutina_id = r.id LIMIT 1) as repeticiones,
                       (SELECT peso FROM series s WHERE s.rutina_id = r.id LIMIT 1) as peso
                FROM rutinas r
                LEFT JOIN ejercicios e ON r.ejercicio_id = e.id
                WHERE r.alumno_id = $alumnoId
                  AND CAST(r.nombre_semana AS UNSIGNED) = $semana
                  AND r.dyear = '$anio'
                  AND CAST(r.dia_semana AS UNSIGNED) = $dia
                ORDER BY r.id ASC";

        $res = $this->db->query($sql);
        $ejercicios = [];

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                // Estado completado: si fecha_fin no es 0000-00-00
                $completado = ($row['fecha_fin'] && $row['fecha_fin'] != '0000-00-00');

                $ejercicios[] = array_merge($row, ['completado' => $completado]);
            }
        }
        return $ejercicios;
    }

    public function marcarEjercicioCompletado($rutinaId)
    {
        // En v3 marcan la fecha_fin. Si ya tiene fecha, la resetean (toggle) o solo completan. 
        // Vamos a asumir toggle para mejor UX.

        $id = $this->escape($rutinaId);

        // Verificar estado actual
        $check = $this->db->query("SELECT fecha_fin FROM rutinas WHERE id = '$id'");
        $row = $check->fetch_assoc();

        if ($row['fecha_fin'] && $row['fecha_fin'] != '0000-00-00') {
            // Desmarcar
            $sql = "UPDATE rutinas SET fecha_fin = '0000-00-00' WHERE id = '$id'";
            $nuevoEstado = false;
        } else {
            // Marcar (fecha de hoy)
            $sql = "UPDATE rutinas SET fecha_fin = CURDATE() WHERE id = '$id'";
            $nuevoEstado = true;
        }

        $this->db->query($sql);
        return $nuevoEstado;
    }

    // =========================================================
    //  MÉTODOS DE ASIGNACIÓN (ADMIN / PROFE / AUTO)
    // =========================================================

    // 1. Insertar Rutina Base
    public function insertarRutina($alumno_id, $profesor_id, $categoria_id, $ejercicio_id, $dia_semana, $semana, $mes, $dyear, $grupo_id = 0)
    {
        $sql = "INSERT INTO rutinas (alumno_id, profesor_id, categoria_id, ejercicio_id, dia_semana, nombre_semana, mes, dyear, grupo_id) 
                VALUES ('$alumno_id', '$profesor_id', '$categoria_id', '$ejercicio_id', '$dia_semana', '$semana', '$mes', '$dyear', '$grupo_id')";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    // 2. Insertar Serie Detalle
    public function insertarSerie($rutina_id, $repeticiones, $peso)
    {
        $sql = "INSERT INTO series (rutina_id, repeticiones, peso) VALUES ('$rutina_id', '$repeticiones', '$peso')";
        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    // 3. Asignar Plantilla (Rutina Personalizada Pre-armada)
    public function asignarPlantillaAAlumno($alumno_id, $profesor_id, $ejercicios, $datosExtras)
    {
        // Calcular nuevo grupo_id
        $res = $this->db->query("SELECT MAX(grupo_id) as max_val FROM rutinas");
        $row = $res->fetch_assoc();
        $grupo_id = (int) ($row['max_val'] ?? 0) + 1;

        $ok = true;
        foreach ($ejercicios as $ej) {
            // Mapeo flexible de claves (acepta snake_case de DB o camelCase de JSON JS)
            $catId = $ej['categoria_id'] ?? 0;
            $ejId = $ej['ejercicios_id'] ?? ($ej['id'] ?? 0); // Ojo aquí con el nombre

            // Si viene de 'ejercicios_rutinas_personalizadas', la columna ejercicio es 'ejercicios_id'
            // Si viene del front puede ser 'ejercicio_id'
            if (!$ejId && isset($ej['ejercicio_id']))
                $ejId = $ej['ejercicio_id'];

            $rutina_id = $this->insertarRutina(
                $alumno_id,
                $profesor_id,
                $catId,
                $ejId,
                $datosExtras['dia_semana'],
                $datosExtras['nombre_semana'],
                $datosExtras['mes'],
                $datosExtras['dyear'],
                $grupo_id
            );

            if ($rutina_id) {
                // Insertar series
                // Si viene de plantilla guardada, 'series' es un INT (cantidad), y reps/peso son fijos
                if (isset($ej['series']) && is_numeric($ej['series'])) {
                    $cant = (int) $ej['series'];
                    $reps = $ej['repeticiones'] ?? 0;
                    $peso = $ej['peso'] ?? 0;
                    for ($i = 0; $i < $cant; $i++) {
                        $this->insertarSerie($rutina_id, $reps, $peso);
                    }
                }
                // Si viene de una UI dinámica que manda array de series (V3 asignacion manual)
                elseif (isset($ej['series_data']) && is_array($ej['series_data'])) {
                    foreach ($ej['series_data'] as $s) {
                        $this->insertarSerie($rutina_id, $s['reps'], $s['peso']);
                    }
                }
            } else {
                $ok = false;
            }
        }
        return $ok;
    }

    // 4. Gestión de Plantillas (Rutinas Personalizadas)
    // Tabla: rutinas_personalizadas (id, nombre, profesores_id)
    public function listarPlantillas($profesor_id = null)
    {
        $sql = "SELECT * FROM rutinas_personalizadas";
        if ($profesor_id) {
            $sql .= " WHERE profesores_id = '$profesor_id' OR profesores_id = 13"; // + Admin/Default
        }
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Tabla: ejercicios_rutinas_personalizadas (rutinas_personalizadas_id, ejercicios_id, categoria_id, series, repeticiones, peso)
    public function obtenerDetallePlantilla($plantilla_id)
    {
        $id = $this->escape($plantilla_id);
        $sql = "SELECT erp.*, e.nombre as ejercicio_nombre, c.nombre as categoria_nombre, e.img as imagen
                FROM ejercicios_rutinas_personalizadas erp
                JOIN ejercicios e ON erp.ejercicios_id = e.id
                JOIN categorias_ejercicios c ON erp.categoria_id = c.id
                WHERE erp.rutinas_personalizadas_id = '$id'";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function guardarPlantilla($nombre, $profesor_id, $ejercicios)
    {
        $this->db->begin_transaction();
        try {
            $nombre = $this->escape($nombre);
            $profesor_id = (int) $profesor_id;

            $sql = "INSERT INTO rutinas_personalizadas (nombre, profesores_id) VALUES ('$nombre', '$profesor_id')";
            if (!$this->db->query($sql))
                throw new Exception("Error al crear cabecera de plantilla");

            $plantilla_id = $this->db->insert_id;

            foreach ($ejercicios as $ej) {
                $ejId = (int) $ej['ejercicio_id'];
                $catId = (int) $ej['categoria_id'];
                $series = (int) $ej['series'];
                $reps = (int) $ej['repeticiones'];
                $peso = $this->escape($ej['peso']);

                $sqlEj = "INSERT INTO ejercicios_rutinas_personalizadas 
                          (rutinas_personalizadas_id, ejercicios_id, categoria_id, series, repeticiones, peso) 
                          VALUES ('$plantilla_id', '$ejId', '$catId', '$series', '$reps', '$peso')";

                if (!$this->db->query($sqlEj))
                    throw new Exception("Error al insertar ejercicio en plantilla");
            }

            $this->db->commit();
            return $plantilla_id;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function eliminarPlantilla($id)
    {
        $id = (int) $id;
        $this->db->begin_transaction();
        try {
            // Eliminar detalles primero
            $this->db->query("DELETE FROM ejercicios_rutinas_personalizadas WHERE rutinas_personalizadas_id = $id");
            // Eliminar cabecera
            $this->db->query("DELETE FROM rutinas_personalizadas WHERE id = $id");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    // Helper: Listar Ejercicios para el Select
    public function listarEjerciciosAgrupados()
    {
        $sqlCat = "SELECT * FROM categorias_ejercicios ORDER BY nombre";
        $cats = $this->db->query($sqlCat)->fetch_all(MYSQLI_ASSOC);

        $resultado = [];
        foreach ($cats as $c) {
            $sqlEj = "SELECT * FROM ejercicios WHERE categoria_id = " . $c['id'] . " ORDER BY nombre";
            $ejs = $this->db->query($sqlEj)->fetch_all(MYSQLI_ASSOC);
            $c['ejercicios'] = $ejs;
            $resultado[] = $c;
        }
        return $resultado;
    }

    // --- GESTIÓN DE SERIES Y EDICIÓN ---

    public function obtenerSeries($rutinaId)
    {
        $sql = "SELECT id, repeticiones, peso FROM series WHERE rutina_id = " . $this->escape($rutinaId) . " ORDER BY id ASC";
        $res = $this->db->query($sql);
        $series = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $series[] = $row;
            }
        }
        return $series;
    }

    public function actualizarSerie($serieId, $reps, $peso)
    {
        $reps = (int) $reps;
        $peso = $this->escape($peso); // Puede ser float o string
        $id = (int) $serieId;

        $sql = "UPDATE series SET repeticiones = '$reps', peso = '$peso' WHERE id = $id";
        return $this->db->query($sql);
    }

    public function eliminarEjercicio($rutinaId)
    {
        $rutinaId = (int) $rutinaId;
        // Eliminar series primero (si no hay Foreign Key Cascade)
        $this->db->query("DELETE FROM series WHERE rutina_id = $rutinaId");

        // Eliminar asignación de rutina
        return $this->db->query("DELETE FROM rutinas WHERE id = $rutinaId");
    }

    public function eliminarSerie($serieId)
    {
        $id = (int) $serieId;
        // Opcional: Verificar que no sea la única serie si se quisiera impedir borrar todo
        return $this->db->query("DELETE FROM series WHERE id = $id");
    }
}
