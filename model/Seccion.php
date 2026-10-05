<?php
class Seccion extends Model
{
    protected $table = 'seccion';

    public function listarCompleto()
    {
        $sql = "SELECT s.*,
                       p.nombre AS periodo,
                       (SELECT COUNT(*) FROM estudiante_seccion es WHERE es.id_seccion = s.id) AS total_estudiantes,
                       COALESCE(GROUP_CONCAT(DISTINCT CONCAT(m.nombre, '||', COALESCE(u.nombre, '')) ORDER BY m.nombre SEPARATOR ';'), '') AS materias_raw
                FROM seccion s
                INNER JOIN periodo_academico p ON p.id = s.id_periodo
                LEFT JOIN seccion_materia sm ON sm.id_seccion = s.id
                LEFT JOIN materia m ON m.id = sm.id_materia
                LEFT JOIN usuario u ON u.id = sm.id_docente
                GROUP BY s.id, s.nombre, s.id_periodo, s.created_at, p.nombre
                ORDER BY p.fecha_inicio DESC, s.nombre ASC";
        $rows = $this->db->fetchAll($sql);
        foreach ($rows as &$row) {
            $row['materias'] = [];
            if ($row['materias_raw'] !== '') {
                foreach (explode(';', $row['materias_raw']) as $par) {
                    $partes = explode('||', $par);
                    $row['materias'][] = [
                        'nombre'  => $partes[0],
                        'docente' => $partes[1] ?? '',
                    ];
                }
            }
        }
        return $rows;
    }

    public function findCompleto($id)
    {
        $sql = "SELECT s.*, p.nombre AS periodo
                FROM seccion s
                INNER JOIN periodo_academico p ON p.id = s.id_periodo
                WHERE s.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function materiasDeSeccion($idSeccion)
    {
        $sql = "SELECT m.id, m.nombre, sm.id_docente, u.nombre AS docente
                FROM seccion_materia sm
                INNER JOIN materia m ON m.id = sm.id_materia
                LEFT JOIN usuario u ON u.id = sm.id_docente
                WHERE sm.id_seccion = ?
                ORDER BY m.nombre";
        return $this->db->fetchAll($sql, [$idSeccion]);
    }

    /**
     * Asigna las materias de la seccion con su docente.
     * $asignaciones: lista de ['id_materia' => int, 'id_docente' => int]
     */
    public function asignarMaterias($idSeccion, array $asignaciones)
    {
        $this->db->execute("DELETE FROM seccion_materia WHERE id_seccion = ?", [$idSeccion]);
        $ok = 0;
        foreach ($asignaciones as $asig) {
            $idMat = (int)($asig['id_materia'] ?? 0);
            $idDoc = (int)($asig['id_docente'] ?? 0);
            if ($idMat <= 0 || $idDoc <= 0) continue;
            $sql = "INSERT IGNORE INTO seccion_materia (id_seccion, id_materia, id_docente) VALUES (?, ?, ?)";
            $this->db->execute($sql, [$idSeccion, $idMat, $idDoc]);
            $ok++;
        }
        return $ok;
    }

    public function listarDocentes()
    {
        $sql = "SELECT id, nombre, correo FROM usuario WHERE rol = 2 AND estado = 1 ORDER BY nombre";
        return $this->db->fetchAll($sql);
    }

    public function listarMateriasDelDocente($idDocente)
    {
        $sql = "SELECT m.id, m.nombre
                FROM docente_materia dm
                INNER JOIN materia m ON m.id = dm.id_materia
                WHERE dm.id_docente = ?
                ORDER BY m.nombre";
        return $this->db->fetchAll($sql, [$idDocente]);
    }

    public function materiasDeEstudiante($idEstudiante)
    {
        $sql = "SELECT DISTINCT m.id, m.nombre
                FROM estudiante_seccion es
                INNER JOIN seccion_materia sm ON sm.id_seccion = es.id_seccion
                INNER JOIN materia m ON m.id = sm.id_materia
                WHERE es.id_estudiante = ?
                ORDER BY m.nombre";
        return $this->db->fetchAll($sql, [$idEstudiante]);
    }

    public function listarEstudiantesDisponibles($idSeccion = null)
    {
        $sql = "SELECT id, nombre, correo
                FROM usuario
                WHERE rol = 3 AND estado = 1
                  AND id NOT IN (
                      SELECT id_estudiante FROM estudiante_seccion
                  )
                ORDER BY nombre";
        return $this->db->fetchAll($sql);
    }

    public function estudiantesAsignados($idSeccion)
    {
        $sql = "SELECT u.id, u.nombre, u.correo, es.created_at
                FROM estudiante_seccion es
                INNER JOIN usuario u ON u.id = es.id_estudiante
                WHERE es.id_seccion = ?
                ORDER BY u.nombre";
        return $this->db->fetchAll($sql, [$idSeccion]);
    }

    public function asignarEstudiantes($idSeccion, array $idsEstudiantes)
    {
        $ok = 0;
        foreach ($idsEstudiantes as $idEst) {
            $idEst = (int)$idEst;
            if ($idEst <= 0) continue;

            $sql = "INSERT IGNORE INTO estudiante_seccion (id_estudiante, id_seccion) VALUES (?, ?)";
            $this->db->execute($sql, [$idEst, $idSeccion]);
            $ok++;
        }
        return $ok;
    }

    public function quitarEstudiante($idSeccion, $idEstudiante)
    {
        $sql = "DELETE FROM estudiante_seccion WHERE id_seccion = ? AND id_estudiante = ?";
        return $this->db->execute($sql, [$idSeccion, $idEstudiante]);
    }

    public function seccionesPorEstudiante($idEstudiante)
    {
        $sql = "SELECT s.*, p.nombre AS periodo
                FROM estudiante_seccion es
                INNER JOIN seccion s ON s.id = es.id_seccion
                INNER JOIN periodo_academico p ON p.id = s.id_periodo
                WHERE es.id_estudiante = ?";
        return $this->db->fetchAll($sql, [$idEstudiante]);
    }

    public function seccionesPorDocente($idDocente)
    {
        $sql = "SELECT DISTINCT s.*, p.nombre AS periodo
                FROM seccion_materia sm
                INNER JOIN seccion s ON s.id = sm.id_seccion
                INNER JOIN periodo_academico p ON p.id = s.id_periodo
                WHERE sm.id_docente = ?
                ORDER BY s.nombre";
        return $this->db->fetchAll($sql, [$idDocente]);
    }

    public function seccionesDelDocenteConMateria($idDocente, $idMateria)
    {
        $sql = "SELECT s.id, s.nombre
                FROM seccion s
                INNER JOIN seccion_materia sm ON sm.id_seccion = s.id
                WHERE sm.id_docente = ? AND sm.id_materia = ?
                ORDER BY s.nombre";
        return $this->db->fetchAll($sql, [$idDocente, $idMateria]);
    }
}
