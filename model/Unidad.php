<?php
class Unidad extends Model
{
    protected $table = 'unidad';

    public function listarPorNivel($idNivel, $soloPublicadas = true, $materiasIds = null)
    {
        $sql = "SELECT u.*, m.nombre AS materia_nombre,
                       (SELECT COUNT(*) FROM unidad_archivo ua WHERE ua.id_unidad = u.id) AS total_archivos
                FROM unidad u
                INNER JOIN materia m ON m.id = u.id_materia
                WHERE u.id_nivel = ?"
                . ($soloPublicadas ? " AND u.estado = 1" : "")
                . $this->filtroMaterias($materiasIds) .
                " ORDER BY u.orden, u.id";
        return $this->db->fetchAll($sql, [$idNivel]);
    }

    public function listarPorMateria($idMateria, $soloPublicadas = true)
    {
        $sql = "SELECT u.*,
                       (SELECT COUNT(*) FROM unidad_archivo ua WHERE ua.id_unidad = u.id) AS total_archivos
                FROM unidad u
                WHERE u.id_materia = ?"
                . ($soloPublicadas ? " AND u.estado = 1" : "") .
                " ORDER BY u.orden, u.id";
        return $this->db->fetchAll($sql, [$idMateria]);
    }

    public function findCompleta($id)
    {
        $sql = "SELECT u.*, n.nombre AS nivel, n.numero AS nivel_numero, m.nombre AS materia
                FROM unidad u
                INNER JOIN nivel n ON n.id = u.id_nivel
                INNER JOIN materia m ON m.id = u.id_materia
                WHERE u.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function archivos($idUnidad)
    {
        $sql = "SELECT * FROM unidad_archivo WHERE id_unidad = ? ORDER BY tipo, id";
        return $this->db->fetchAll($sql, [$idUnidad]);
    }

    public function siguienteOrden($idNivel)
    {
        $sql = "SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente FROM unidad WHERE id_nivel = ?";
        $row = $this->db->fetch($sql, [$idNivel]);
        return (int)($row['siguiente'] ?? 1);
    }

    private function filtroMaterias($materiasIds)
    {
        if ($materiasIds === null) {
            return '';
        }
        $materiasIds = array_values(array_map('intval', (array)$materiasIds));
        if (empty($materiasIds)) {
            return " AND u.id_materia IN (0)";
        }
        return " AND u.id_materia IN (" . implode(',', $materiasIds) . ")";
    }
}
