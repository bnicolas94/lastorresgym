<?php
class FinanzasModel extends Model
{
    public function obtenerIngresosHoy()
    {
        $hoy = date('Y-m-d');
        // Sumar 'monto' de 'pagos' donde fecha_pago sea hoy y el estado sea 'approved' o equivalente
        // Asumiendo que 'estado' para efectivo confirmado es 'approved' o 'completed'
        $sql = "SELECT SUM(monto) as total FROM pagos WHERE DATE(fecha_pago) = '$hoy' AND (estado = 'approved' OR estado = 'completed')";
        $res = $this->db->query($sql);
        return $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function obtenerIngresosMes()
    {
        $mes = date('m');
        $ano = date('Y');
        $sql = "SELECT SUM(monto) as total FROM pagos 
                WHERE MONTH(fecha_pago) = '$mes' AND YEAR(fecha_pago) = '$ano' 
                AND (estado = 'approved' OR estado = 'completed')";
        $res = $this->db->query($sql);
        return $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;
    }

    // --- HELPERS CICLO DE CAJA ---
    public function obtenerUltimoArqueo()
    {
        // Get the latest closing time
        $sql = "SELECT hora_cierre FROM arqueos_caja ORDER BY hora_cierre DESC LIMIT 1";
        $res = $this->db->query($sql);
        if ($res && $row = $res->fetch_assoc()) {
            return $row['hora_cierre'];
        }
        return null; // Never closed
    }

    public function obtenerIngresosEfectivoHoy()
    {
        $ultimo_cierre = $this->obtenerUltimoArqueo();
        $whereClause = "";

        if ($ultimo_cierre) {
            // Count everything AFTER the last close
            $whereClause = "fecha_pago > '$ultimo_cierre'";
        } else {
            // Fallback: Count everything from today (or all time if first run, but let's stick to today/all)
            // Ideally if never closed, it's everything in 'pending' state essentially.
            // Let's assume 'Current Shift' starts at 0 if no close exist, or just today.
            // For safety, let's strictly count 'Today' if no prior close, OR 'Since Beginning' 
            // In a continuous system, it should be 'Since Beginning'. Let's use 'Today' as safe default
            $hoy = date('Y-m-d');
            $whereClause = "DATE(fecha_pago) >= '$hoy'";
        }

        $sql = "SELECT SUM(monto) as total FROM pagos 
                WHERE $whereClause 
                AND metodo_pago = 'efectivo'
                AND (estado = 'approved' OR estado = 'completed')";
        $res = $this->db->query($sql);
        return $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function obtenerRetirosHoy()
    {
        $ultimo_cierre = $this->obtenerUltimoArqueo();
        $whereClause = "";

        if ($ultimo_cierre) {
            $whereClause = "fecha_retiro > '$ultimo_cierre'";
        } else {
            $hoy = date('Y-m-d');
            $whereClause = "DATE(fecha_retiro) >= '$hoy'";
        }

        $sql = "SELECT SUM(monto) as total FROM retiros_caja WHERE $whereClause";
        $res = $this->db->query($sql);
        return $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;
    }

    // Also update this one for consistency
    public function obtenerEfectivoCaja()
    {
        return $this->obtenerIngresosEfectivoHoy() - $this->obtenerRetirosHoy();
    }

    public function registrarRetiro($usuario_id, $monto, $concepto)
    {
        $usuario_id = (int) $usuario_id;
        $monto = $this->escape($monto);
        $concepto = $this->escape($concepto);
        $fecha = date('Y-m-d H:i:s');

        $sql = "INSERT INTO retiros_caja (usuario_id, monto, concepto, fecha_retiro) 
                VALUES ($usuario_id, '$monto', '$concepto', '$fecha')";

        if ($this->db->query($sql)) {
            return true;
        } else {
            return $this->db->getConexion()->error;
        }
    }

    public function obtenerUltimosMovimientos($limit = 10)
    {
        // Join con alumnos para saber quién pagó
        $sql = "SELECT p.*, a.nombre, a.apellido, a.foto 
                FROM pagos p 
                JOIN alumnos a ON p.alumno_id = a.id 
                ORDER BY p.fecha_pago DESC LIMIT $limit";

        $res = $this->db->query($sql);
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function obtenerDistribucionMetodos()
    {
        $mes = date('m');
        // Agrupar por metodo_pago este mes
        $sql = "SELECT metodo_pago, SUM(monto) as total 
                FROM pagos 
                WHERE MONTH(fecha_pago) = '$mes'
                GROUP BY metodo_pago";

        $res = $this->db->query($sql);
        $labels = [];
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $labels[] = ucfirst($row['metodo_pago']); // 'Efectivo', 'Mercadopago'
                $data[] = $row['total'];
            }
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function registrarPago($alumno_id, $monto, $concepto, $metodo, $observaciones, $destino = null)
    {
        $alumno_id = $this->escape($alumno_id);
        $monto = $this->escape($monto);
        $concepto = $this->escape($concepto);
        $metodo = $this->escape($metodo);
        $observaciones = $this->escape($observaciones);
        $destino = $this->escape($destino); // Nuevo
        $fecha = date('Y-m-d H:i:s');
        $estado = 'approved';

        $sql = "INSERT INTO pagos (alumno_id, monto, concepto, metodo_pago, observaciones, fecha_pago, fecha_creacion, estado, destino) 
                VALUES ('$alumno_id', '$monto', '$concepto', '$metodo', '$observaciones', '$fecha', '$fecha', '$estado', '$destino')";

        return $this->db->query($sql);
    }

    public function obtenerEstadisticasTransferencias()
    {
        // Also respect the "Shift" logic? Usually transfers are reconciled daily.
        // The user asked for "Daily" closing. 
        // If we use "Cycle" logic, it allows checking transfers since last check.
        // Let's stick to "Today" for transfers as they are bank-dependant, 
        // OR align with the shift to avoid confusion.
        // User said: "registro para el dia siguiente". Implies Shift.

        $ultimo_cierre = $this->obtenerUltimoArqueo();
        $whereClause = "";

        if ($ultimo_cierre) {
            $whereClause = "fecha_pago > '$ultimo_cierre'";
        } else {
            $hoy = date('Y-m-d');
            $whereClause = "DATE(fecha_pago) >= '$hoy'";
        }

        $sql = "SELECT destino, SUM(monto) as total 
                FROM pagos 
                WHERE $whereClause
                AND metodo_pago = 'transferencia'
                AND (estado = 'approved' OR estado = 'completed')
                GROUP BY destino";

        $res = $this->db->query($sql);
        $stats = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $key = !empty($row['destino']) ? $row['destino'] : 'Otros';
                $stats[$key] = $row['total'];
            }
        }
        return $stats;
    }

    // --- MOVIMIENTOS UNIFICADOS ---
    public function obtenerMovimientosUnificados($limit = 50, $fechaFiltro = null)
    {
        // Pagos: tipo='ingreso', id, monto, concepto(articulo), detalle, fecha
        // Retiros: tipo='retiro', id, monto, concepto, detalle, fecha

        // Enclose status OR check in parentheses to avoid precedence issues with input AND
        $wherePagos = "(p.estado = 'approved' OR p.estado = 'completed')";
        $whereRetiros = "1=1";
        $whereArqueos = "1=1";

        if ($fechaFiltro) {
            // Filter by specific DATE (Calendar Day)
            $fechaFiltro = $this->escape($fechaFiltro);
            $wherePagos .= " AND DATE(p.fecha_pago) = '$fechaFiltro'";
            $whereRetiros .= " AND DATE(r.fecha_retiro) = '$fechaFiltro'";
            $whereArqueos .= " AND DATE(a.fecha) = '$fechaFiltro'";
        }

        $sql = "
        (SELECT 
            p.id, 
            'ingreso' as tipo, 
            p.monto, 
            CONCAT('Pago: ', p.concepto) as concepto,
            CONCAT(al.nombre, ' ', al.apellido) as detalle_usuario,
            p.fecha_pago as fecha,
            p.metodo_pago,
            p.destino
         FROM pagos p
         JOIN alumnos al ON p.alumno_id = al.id
         WHERE $wherePagos)
         
        UNION ALL
        
        (SELECT 
            r.id, 
            'retiro' as tipo, 
            r.monto, 
            CONCAT('Retiro: ', r.concepto) as concepto,
            CONCAT(u.nombre, ' ', u.apellido) as detalle_usuario,
            r.fecha_retiro as fecha,
            'efectivo' as metodo_pago,
            NULL as destino
         FROM retiros_caja r
         JOIN alumnos u ON r.usuario_id = u.id
         WHERE $whereRetiros)

        UNION ALL

        (SELECT
            a.id,
            'cierre' as tipo,
            a.efectivo_real as monto, -- Mostrar el real contado
            'Cierre de Caja' as concepto,
            CONCAT(u2.nombre, ' ', u2.apellido) as detalle_usuario,
            a.hora_cierre as fecha,
            'efectivo' as metodo_pago,
            NULL as destino
         FROM arqueos_caja a
         JOIN alumnos u2 ON a.usuario_id = u2.id
         WHERE $whereArqueos)
         
        ORDER BY fecha DESC
        LIMIT $limit
        ";

        $res = $this->db->query($sql);
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // --- ARQUEO ---
    public function registrarArqueo($datos)
    {
        $usuario_id = (int) $datos['usuario_id'];
        $fecha = date('Y-m-d'); // Hoy
        $ingresos = $this->escape($datos['ingresos_sistema']);
        $retiros = $this->escape($datos['retiros_sistema']);
        $saldo = $this->escape($datos['saldo_sistema']);
        $real = $this->escape($datos['efectivo_real']);
        $diferencia = $this->escape($datos['diferencia']);
        $observaciones = $this->escape($datos['observaciones']);
        $hora = date('Y-m-d H:i:s');

        $sql = "INSERT INTO arqueos_caja (fecha, hora_cierre, ingresos_sistema, retiros_sistema, saldo_sistema, efectivo_real, diferencia, usuario_id, observaciones)
                VALUES ('$fecha', '$hora', '$ingresos', '$retiros', '$saldo', '$real', '$diferencia', '$usuario_id', '$observaciones')";

        if ($this->db->query($sql)) {
            return true;
        } else {
            return $this->db->getConexion()->error;
        }
    }

    public function eliminarArqueo($id)
    {
        $id = (int) $id;
        $sql = "DELETE FROM arqueos_caja WHERE id = $id";
        return $this->db->query($sql);
    }
}
