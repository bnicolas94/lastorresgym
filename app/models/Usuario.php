<?php
class Usuario extends Model
{

    public function obtenerPorDni($dni)
    {
        $dni = $this->escape($dni);
        $sql = "SELECT * FROM alumnos WHERE dni = '$dni' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null; // No encontrado
    }

    public function obtenerPorId($id)
    {
        $id = $this->escape($id);
        $sql = "SELECT * FROM alumnos WHERE id = '$id' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function actualizarPerfil($id, $data)
    {
        $id = $this->escape($id);
        $telefono = $this->escape($data['telefono']);
        $email = $this->escape($data['email']);
        $direccion = $this->escape($data['direccion']);

        $sql = "UPDATE alumnos SET telefono = '$telefono', email = '$email', direccion = '$direccion' WHERE id = '$id'";
        return $this->db->query($sql);
    }

    public function actualizarFoto($id, $urlFoto)
    {
        $id = $this->escape($id);
        $urlFoto = $this->escape($urlFoto);
        $sql = "UPDATE alumnos SET foto = '$urlFoto' WHERE id = '$id'";
        return $this->db->query($sql);
    }

    public function actualizarPassword($id, $newHash)
    {
        $id = $this->escape($id);
        // El hash ya viene listo desde el controlador
        $sql = "UPDATE alumnos SET password = '$newHash' WHERE id = '$id'";
        return $this->db->query($sql);
    }

    public function verificarPassword($id, $passwordPlain)
    {
        $user = $this->obtenerPorId($id);
        if ($user && password_verify($passwordPlain, $user['password'])) {
            return true;
        }
        return false;
    }

    public function actualizarUltimoIngreso($id)
    {
        $id = $this->escape($id);
        $this->db->query("UPDATE alumnos SET ultima_actividad = NOW() WHERE id = '$id'");
    }

    // --- MÉTODOS PARA REMEMBER ME ---

    public function guardarToken($userId, $tokenHash, $expiry)
    {
        $userId = $this->escape($userId);
        $tokenHash = $this->escape($tokenHash);
        $expiry = $this->escape($expiry);

        $sql = "INSERT INTO user_tokens (user_id, token_hash, expiry) VALUES ('$userId', '$tokenHash', '$expiry')";
        return $this->db->query($sql);
    }

    public function obtenerUsuarioPorTokenHash($tokenHash)
    {
        $tokenHash = $this->escape($tokenHash);

        // Unimos con la tabla alumnos para obtener datos del usuario si el token es válido y no expiró
        $sql = "SELECT u.* 
                FROM user_tokens t
                JOIN alumnos u ON t.user_id = u.id
                WHERE t.token_hash = '$tokenHash' 
                AND t.expiry > NOW()
                LIMIT 1";

        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function eliminarToken($tokenHash)
    {
        $tokenHash = $this->escape($tokenHash);
        $sql = "DELETE FROM user_tokens WHERE token_hash = '$tokenHash'";
        return $this->db->query($sql);
    }

    public function eliminarTokensExpirados()
    {
        $this->db->query("DELETE FROM user_tokens WHERE expiry < NOW()");
    }
}
?>