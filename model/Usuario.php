<?php
class Usuario extends Model
{
    protected $table = 'usuario';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Autentica un usuario por correo y contrasena.
     * Soporta hashes de password_hash() y migracion automatica desde MD5.
     */
    public function autenticar($correo, $clave)
    {
        $sql = "SELECT * FROM {$this->table} WHERE correo = ? AND estado = 1 LIMIT 1";
        $usuario = $this->db->fetch($sql, [$correo]);

        if (!$usuario) {
            return false;
        }

        $hashGuardado = $usuario['clave'];

        // 1) Intentar con password_verify (hash moderno)
        if (password_verify($clave, $hashGuardado)) {
            return $usuario;
        }

        // 2) Compatibilidad: si el hash guardado es MD5, verificar y migrar
        if (strlen($hashGuardado) === 32 && ctype_xdigit($hashGuardado)) {
            if (md5($clave) === $hashGuardado) {
                $nuevoHash = password_hash($clave, PASSWORD_DEFAULT);
                $this->db->execute(
                    "UPDATE {$this->table} SET clave = ? WHERE id = ?",
                    [$nuevoHash, $usuario['id']]
                );
                $usuario['clave'] = $nuevoHash;
                return $usuario;
            }
        }

        return false;
    }

    public function buscar($termino)
    {
        $sql = "SELECT * FROM {$this->table} WHERE nombre LIKE ? OR correo LIKE ? OR telefono LIKE ?";
        $param = "%{$termino}%";
        return $this->db->fetchAll($sql, [$param, $param, $param]);
    }

    /**
     * Verifica si un correo ya existe (exceptuando un ID opcional)
     */
    public function existeCorreo($correo, $excluirId = null)
    {
        if ($excluirId) {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE correo = ? AND id != ?";
            $result = $this->db->fetch($sql, [$correo, $excluirId]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE correo = ?";
            $result = $this->db->fetch($sql, [$correo]);
        }
        return $result['total'] > 0;
    }

    /**
     * Crea un usuario con la contrasena ya hasheada.
     */
    public function crearUsuario($data, $clave)
    {
        $data['clave'] = password_hash($clave, PASSWORD_DEFAULT);
        return $this->create($data);
    }

    /**
     * Actualiza la contrasena del usuario.
     */
    public function actualizarClave($id, $clave)
    {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        return $this->update($id, ['clave' => $hash]);
    }

    /**
     * Lista usuarios por rol
     */
    public function listarPorRol($rol)
    {
        $sql = "SELECT * FROM {$this->table} WHERE rol = ? AND estado = 1 ORDER BY nombre";
        return $this->db->fetchAll($sql, [$rol]);
    }
}
