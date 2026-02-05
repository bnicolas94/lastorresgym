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

    public function obtenerEfectivoCaja()
    {
        // Ingresos en efectivo de HOY (para arqueo rápido)
        $hoy = date('Y-m-d');
        $sql = "SELECT SUM(monto) as total FROM pagos 
                WHERE DATE(fecha_pago) = '$hoy' 
                AND metodo_pago = 'efectivo'
                AND (estado = 'approved' OR estado = 'completed')";
        $res = $this->db->query($sql);
        return $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;
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

    public function registrarPago($alumno_id, $monto, $concepto, $metodo, $observaciones)
    {
        $alumno_id = $this->escape($alumno_id);
        $monto = $this->escape($monto);
        $concepto = $this->escape($concepto);
        $metodo = $this->escape($metodo);
        $observaciones = $this->escape($observaciones);
        $fecha = date('Y-m-d H:i:s');
        $estado = 'approved'; // Manual payments are approved instantly

        // payment_id and preference_id are null for manual payments
        $sql = "INSERT INTO pagos (alumno_id, monto, concepto, metodo_pago, observaciones, fecha_pago, fecha_creacion, estado) 
                VALUES ('$alumno_id', '$monto', '$concepto', '$metodo', '$observaciones', '$fecha', '$fecha', '$estado')";

        return $this->db->query($sql);
    }
}
