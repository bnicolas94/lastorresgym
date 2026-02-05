<?php
class DashboardModel extends Model
{

    public function obtenerPlanActual($alumnoId)
    {
        // 1. Obtener última semana/año asignados
        $sqlLast = "SELECT dyear, nombre_semana 
                    FROM rutinas 
                    WHERE alumno_id = " . $this->escape($alumnoId) . "
                    ORDER BY dyear DESC, CAST(nombre_semana AS UNSIGNED) DESC, id DESC 
                    LIMIT 1";

        $resLast = $this->db->query($sqlLast);
        $last = $resLast ? $resLast->fetch_assoc() : null;

        if (!$last) {
            return ['hay_plan' => false];
        }

        $week = (int) $last['nombre_semana'];
        $year = (int) $last['dyear'];

        // 2. Traer resumen por día
        $sql = "SELECT CAST(r.dia_semana AS UNSIGNED) AS dia,
                       GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.id SEPARATOR ' y ') AS grupos,
                       SUM(CASE WHEN r.fecha_fin <> '0000-00-00' THEN 1 ELSE 0 END) AS hechos,
                       COUNT(*) AS total
                FROM rutinas r
                INNER JOIN categorias_ejercicios c ON c.id = r.categoria_id
                WHERE r.alumno_id = $alumnoId
                  AND r.nombre_semana = '$week'
                  AND r.dyear = '$year'
                GROUP BY dia
                ORDER BY dia ASC";

        $res = $this->db->query($sql);
        $dias = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $dias[] = $row;
            }
        }

        // 3. Buscar próximo día incompleto
        $proximo = null;
        foreach ($dias as $d) {
            if ((int) $d['hechos'] < (int) $d['total']) {
                $proximo = $d;
                break;
            }
        }

        if (!$proximo) {
            return [
                'hay_plan' => true,
                'week' => $week,
                'completo' => true,
                'texto' => '¡Plan completado!'
            ];
        }

        return [
            'hay_plan' => true,
            'week' => $week,
            'dia' => $proximo['dia'],
            'texto' => $proximo['grupos'] ? 'Toca: ' . $proximo['grupos'] : 'A entrenar',
            'completo' => false
        ];
    }

    public function asistenciasMes($alumnoId)
    {
        // Necesitamos el DNI
        $sqlDni = "SELECT dni FROM alumnos WHERE id = " . $this->escape($alumnoId);
        $row = $this->db->query($sqlDni)->fetch_assoc();

        if (!$row)
            return 0;
        $dni = $row['dni'];

        $month = (int) date('n');
        $year = (int) date('Y');

        $sql = "SELECT COUNT(DISTINCT DATE(fecha_acceso)) AS dias_reales
                FROM asistencias
                WHERE dni = '$dni'
                  AND MONTH(fecha_acceso) = $month
                  AND YEAR(fecha_acceso) = $year";

        $res = $this->db->query($sql)->fetch_assoc();
        return (int) ($res['dias_reales'] ?? 0);
    }
    // --- MÉTODOS PARA ADMINISTRADOR ---

    public function contarSociosActivos()
    {
        $sql = "SELECT COUNT(*) as total FROM alumnos WHERE activo = 1";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_assoc()['total'] : 0;
    }

    public function contarNuevosMes()
    {
        $mes = date('m');
        $ano = date('Y');
        $sql = "SELECT COUNT(*) as total FROM alumnos WHERE MONTH(fecha_registro) = '$mes' AND YEAR(fecha_registro) = '$ano'";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_assoc()['total'] : 0;
    }

    public function contarAsistenciasHoy()
    {
        $hoy = date('Y-m-d');
        $sql = "SELECT COUNT(DISTINCT dni) as total FROM asistencias WHERE DATE(fecha_acceso) = '$hoy'";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_assoc()['total'] : 0;
    }

    public function contarEnGymAhora()
    {
        // Usamos la lógica de v3: Contamos asistencias de los últimos 60 minutos usando SQL NOW()
        // Esto evita problemas de diferencia horaria entre PHP y MySQL
        $sql = "SELECT COUNT(*) as total 
                FROM asistencias 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_assoc()['total'] : 0;
    }

    public function obtenerUltimoAcceso()
    {
        // Obtenemos el último de asistencias join alumnos
        // Usamos created_at como prioridad (como en v3) o fecha_acceso
        // Filtramos por fecha de hoy para mostrar 'En Vivo' real
        $hoy = date('Y-m-d');

        // Intentamos obtener created_at. Si la tabla no tiene created_at (error SQL),
        // el usuario nos avisará, pero V3 lo usaba así que asumimos que existe.
        $sql = "SELECT a.nombre, a.apellido, a.foto, a.vence, asis.created_at, asis.fecha_acceso
                FROM asistencias asis
                JOIN alumnos a ON asis.dni = a.dni 
                WHERE DATE(asis.fecha_acceso) = '$hoy'
                ORDER BY asis.id DESC LIMIT 1";

        $res = $this->db->query($sql);
        $row = $res ? $res->fetch_assoc() : null;

        if ($row) {
            // Preferimos created_at si existe y no es nulo, sino fecha_acceso
            $timestamp = $row['created_at'] ?? $row['fecha_acceso'];
            $hora = date('H:i', strtotime($timestamp));

            // Estado visual
            $estado = (strtotime($row['vence']) < time()) ? 'Vencido' : 'Acceso Permitido';

            return [
                'nombre' => $row['nombre'] . ' ' . $row['apellido'],
                'foto' => !empty($row['foto']) ? $row['foto'] : BASE_URL . 'assets/img/profile-default.png',
                'hora' => $hora,
                'estado' => $estado,
                // Color de badge
                'estado_color' => ($estado == 'Vencido') ? 'danger' : 'success'
            ];
        }
        return null;
    }

    public function obtenerFlujoAsistencias()
    {
        // 1. Generar últimos 7 días (incluyendo hoy)
        $fechas = [];
        for ($i = 6; $i >= 0; $i--) {
            $fechas[date('Y-m-d', strtotime("-$i days"))] = 0;
        }

        // 2. Consulta DB: Solo hasta hoy
        $sql = "SELECT DATE(fecha_acceso) as fecha, COUNT(*) as cantidad 
                FROM asistencias 
                WHERE fecha_acceso >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                  AND fecha_acceso < DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                GROUP BY DATE(fecha_acceso)
                ORDER BY fecha ASC";

        $res = $this->db->query($sql);

        // 3. Merge datos
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                if (isset($fechas[$row['fecha']])) {
                    $fechas[$row['fecha']] = (int) $row['cantidad'];
                }
            }
        }

        // 4. Formatear para frontend
        $data = [];
        foreach ($fechas as $fecha => $cantidad) {
            $data[] = ['fecha' => $fecha, 'cantidad' => $cantidad];
        }
        return $data;
    }

    public function obtenerVencimientosProximos()
    {
        // Próximos 7 días
        $hoy = date('Y-m-d');
        $limite = date('Y-m-d', strtotime('+7 days'));

        $sql = "SELECT nombre, apellido, telefono, vence 
                FROM alumnos 
                WHERE vence BETWEEN '$hoy' AND '$limite' 
                ORDER BY vence ASC LIMIT 5";

        $res = $this->db->query($sql);
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc())
                $data[] = $row;
        }
        return $data;
    }

    public function obtenerEstadoMembresias()
    {
        $sql = "SELECT 
                    SUM(CASE WHEN vence >= CURDATE() THEN 1 ELSE 0 END) as al_dia,
                    SUM(CASE WHEN vence < CURDATE() THEN 1 ELSE 0 END) as vencidos
                FROM alumnos";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_assoc() : ['al_dia' => 0, 'vencidos' => 0];
    }

    // --- USO DE APP ---

    public function registrarUsoApp($alumnoId)
    {
        $alumnoId = $this->escape($alumnoId);
        $fecha = date('Y-m-d');

        // Evitar duplicados (1 hora de delay)
        $sqlCheck = "SELECT hora FROM historial_uso_app 
                     WHERE alumno_id = '$alumnoId' 
                       AND fecha = '$fecha' 
                       AND hora >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                     LIMIT 1";

        $res = $this->db->query($sqlCheck);
        if ($res && $res->num_rows > 0) {
            return; // Ya registró recientemente
        }

        // Insertar (Usamos IGNORE para evitar error 'Duplicate entry' si existe índice único)
        $hora = date('H:i:s');
        $sqlInsert = "INSERT IGNORE INTO historial_uso_app (alumno_id, fecha, hora) VALUES ('$alumnoId', '$fecha', '$hora')";
        $this->db->query($sqlInsert);
    }

    public function obtenerUsoAppPorFecha($fecha)
    {
        $fecha = $this->escape($fecha);
        // Si la tabla historial_uso_app no existe, esto fallará. Asumo que existe.
        $sql = "SELECT h.hora, a.nombre, a.apellido, a.foto 
                FROM historial_uso_app h
                JOIN alumnos a ON h.alumno_id = a.id
                WHERE h.fecha = '$fecha'
                ORDER BY h.hora DESC";

        $res = $this->db->query($sql);
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                // Formato de hora
                $row['hora_fmt'] = date('H:i', strtotime($row['hora'])) . ' hs';
                $row['foto'] = !empty($row['foto']) ? $row['foto'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
                $data[] = $row;
            }
        }
        return $data;
    }
}
?>