<?php
class DocenteMateria extends Model
{
    protected $table = 'docente_materia';

    public function materiasDeDocente($idDocente)
    {
        $sql = "SELECT m.id, m.nombre
                FROM materia m
                INNER JOIN docente_materia dm ON dm.id_materia = m.id
                WHERE dm.id_docente = ?
                ORDER BY m.nombre";
        return $this->db->fetchAll($sql, [$idDocente]);
    }

    public function docentesDeMateria($idMateria)
    {
        $sql = "SELECT u.id, u.nombre, u.correo
                FROM usuario u
                INNER JOIN docente_materia dm ON dm.id_docente = u.id
                WHERE dm.id_materia = ? AND u.estado = 1
                ORDER BY u.nombre";
        return $this->db->fetchAll($sql, [$idMateria]);
    }

    public function asignar($idDocente, array $idsMaterias)
    {
        $this->db->execute("DELETE FROM docente_materia WHERE id_docente = ?", [$idDocente]);

        $ok = 0;
        foreach ($idsMaterias as $idMateria) {
            $idMateria = (int)$idMateria;
            if ($idMateria <= 0) continue;
            $this->db->execute(
                "INSERT IGNORE INTO docente_materia (id_docente, id_materia) VALUES (?, ?)",
                [$idDocente, $idMateria]
            );
            $ok++;
        }
        return $ok;
    }

    public function docentesPorMateriaParaSeccion($idMateria)
    {
        $sql = "SELECT u.id, u.nombre
                FROM usuario u
                INNER JOIN docente_materia dm ON dm.id_docente = u.id
                WHERE dm.id_materia = ? AND u.estado = 1 AND u.rol = 2
                ORDER BY u.nombre";
        return $this->db->fetchAll($sql, [$idMateria]);
    }
}
