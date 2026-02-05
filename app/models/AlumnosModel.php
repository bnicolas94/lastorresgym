<?php
class AlumnosModel extends Model
{

    // Obtener listado paginado con filtros y ordenamiento
    public function listar($busqueda = '', $offset = 0, $limit = 20, $soloActivos = true, $orderBy = 'apellido', $orderDir = 'ASC')
    {
        $busqueda = $this->escape($busqueda);

        // 1. Filtros WHERE
        $where = "1=1";
        if (!empty($busqueda)) {
            $where .= " AND (nombre LIKE '%$busqueda%' OR apellido LIKE '%$busqueda%' OR dni LIKE '%$busqueda%')";
        }
        if ($soloActivos) {
            $where .= " AND vence >= CURDATE()";
        }

        // 2. Ordenamiento Seguro
        $allowedSort = ['nombre', 'apellido', 'vence', 'gym_visitas', 'app_uso'];
        if (!in_array($orderBy, $allowedSort)) {
            $orderBy = 'apellido';
        }
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        // 3. Query Principal con Subqueries para contadores (eficiente para sorting)
        // gym_visitas: cuenta asistencias por dni
        // app_uso: cuenta registros en historial_uso_app por alumno_id
        // app_ultimo: fecha del último uso de la app
        $sql = "SELECT 
                    a.id, a.dni, a.nombre, a.apellido, a.foto, a.telefono, a.vence, a.activo,
                    (SELECT COUNT(*) FROM asistencias WHERE dni = a.dni) as gym_visitas,
                    (SELECT COUNT(*) FROM historial_uso_app WHERE alumno_id = a.id) as app_uso,
                    (SELECT MAX(fecha) FROM historial_uso_app WHERE alumno_id = a.id) as app_ultimo
                FROM alumnos a
                WHERE $where
                ORDER BY $orderBy $orderDir
                LIMIT $offset, $limit";

        $res = $this->db->query($sql);
        $alumnos = [];

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                // Cálculo de Status de Uso App
                $gym = (int) $row['gym_visitas'];
                $app = (int) $row['app_uso'];

                $ratio = ($gym > 0) ? ($app / $gym) : 0;

                $status = 'Bajo';
                $color = 'danger'; // badge-danger (o similar)

                if ($app == 0) {
                    $status = 'Nunca';
                    $color = 'secondary';
                } elseif ($ratio >= 0.7 || $app > 15) {
                    // Si usa la app casi tanto como va al gym (70%) o la usó mucho absoluto (>15 veces)
                    $status = 'Alto';
                    $color = 'success';
                } elseif ($ratio >= 0.3 || $app > 5) {
                    $status = 'Medio';
                    $color = 'warning';
                } else {
                    $status = 'Bajo';
                    $color = 'danger';
                }

                $row['app_status_label'] = $status;
                $row['app_status_color'] = $color;

                // Formato fecha último uso
                $row['app_ultimo_fmt'] = $row['app_ultimo'] ? date('d/m/y', strtotime($row['app_ultimo'])) : '-';

                $alumnos[] = $row;
            }
        }

        // Contar total para paginación
        $sqlCount = "SELECT COUNT(*) as total FROM alumnos a WHERE $where";
        $total = $this->db->query($sqlCount)->fetch_assoc()['total'];

        return ['data' => $alumnos, 'total' => $total];
    }

    public function contarVisitas($dni)
    {
        $dni = $this->escape($dni);
        $sql = "SELECT COUNT(*) as total FROM asistencias WHERE dni = '$dni'";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_assoc()['total'] : 0;
    }

    // Agregar nuevo alumno
    public function agregar($data)
    {
        $dni = $this->escape($data['dni']);
        $nombre = $this->escape($data['nombre']);
        $apellido = $this->escape($data['apellido']);
        $telefono = $this->escape($data['telefono']);

        // Contraseña por defecto: 1234
        $password = password_hash('1234', PASSWORD_DEFAULT);

        // Fecha registro hoy
        $fecha_registro = date('Y-m-d');

        // Vencimiento por defecto (ej: 1 mes) o vacío
        $vence = date('Y-m-d', strtotime('+30 days'));

        $sql = "INSERT INTO alumnos (dni, nombre, apellido, telefono, password, rol_id, fecha_registro, vence, activo) 
                VALUES ('$dni', '$nombre', '$apellido', '$telefono', '$password', 5, '$fecha_registro', '$vence', 1)";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function verificarDni($dni)
    {
        $dni = $this->escape($dni);
        $sql = "SELECT id FROM alumnos WHERE dni = '$dni'";
        $res = $this->db->query($sql);
        return $res && $res->num_rows > 0;
    }

    public function buscarPorDni($dni)
    {
        $dni = $this->escape($dni);
        $sql = "SELECT id, nombre, apellido, foto, vence FROM alumnos WHERE dni = '$dni'";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_assoc() : null;
    }

    // Renovar Cuota (Extender 30 días desde HOY o desde su vencimiento si es futuro)
    public function renovarCuota($alumno_id)
    {
        $id = (int) $alumno_id;
        // Obtener vencimiento actual
        $sql = "SELECT vence FROM alumnos WHERE id = $id";
        $res = $this->db->query($sql);
        $venceActual = $res->fetch_assoc()['vence'];

        $hoy = date('Y-m-d');
        // Si ya venció (fecha anterior a hoy), nuevo vencimiento es hoy + 30 días
        // Si vence en futuro (ej: mañana), se suma 30 días a esa fecha futura
        if ($venceActual < $hoy) {
            $nuevoVence = date('Y-m-d', strtotime('+30 days'));
        } else {
            $nuevoVence = date('Y-m-d', strtotime($venceActual . ' +30 days'));
        }

        $sqlUp = "UPDATE alumnos SET vence = '$nuevoVence' WHERE id = $id";
        return $this->db->query($sqlUp);
    }
}
?>