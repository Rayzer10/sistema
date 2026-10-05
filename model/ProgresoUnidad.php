<?php
class ProgresoUnidad extends Model
{
    protected $table = 'progreso_unidad';

    /**
     * Marca una unidad como completada para un estudiante (idempotente)
     */
    public function marcar($idEstudiante, $idUnidad)
    {
        $sql = "INSERT IGNORE INTO progreso_unidad (id_estudiante, id_unidad, completada) VALUES (?, ?, 1)";
        return $this->db->execute($sql, [$idEstudiante, $idUnidad]);
    }

    public function desmarcar($idEstudiante, $idUnidad)
    {
        $sql = "DELETE FROM progreso_unidad WHERE id_estudiante = ? AND id_unidad = ?";
        return $this->db->execute($sql, [$idEstudiante, $idUnidad]);
    }

    public function estaCompletada($idEstudiante, $idUnidad)
    {
        $sql = "SELECT id FROM progreso_unidad WHERE id_estudiante = ? AND id_unidad = ? LIMIT 1";
        $row = $this->db->fetch($sql, [$idEstudiante, $idUnidad]);
        return $row !== false && $row !== null;
    }

    public function unidadesCompletadasNivel($idEstudiante, $idNivel, $materiasIds = null)
    {
        $sql = "SELECT u.id
                FROM progreso_unidad pu
                INNER JOIN unidad u ON u.id = pu.id_unidad
                WHERE pu.id_estudiante = ? AND u.id_nivel = ? AND u.estado = 1"
                . $this->filtroMaterias($materiasIds, 'u');
        $rows = $this->db->fetchAll($sql, [$idEstudiante, $idNivel]);
        return array_map(fn($r) => (int)$r['id'], $rows);
    }

    /**
     * Resumen de progreso por nivel: total unidades, completadas, porcentaje.
     * Si $materiasIds no es null, solo cuenta unidades de esas materias.
     */
    public function resumenPorNivel($idEstudiante, $materiasIds = null)
    {
        $sql = "SELECT n.id AS nivel_id,
                       n.numero,
                       n.nombre,
                       n.porcentaje_desbloqueo,
                       (SELECT COUNT(*) FROM unidad u WHERE u.id_nivel = n.id AND u.estado = 1"
                       . $this->filtroMaterias($materiasIds, 'u') . ") AS total_unidades,
                       (SELECT COUNT(*) FROM progreso_unidad pu
                        INNER JOIN unidad u2 ON u2.id = pu.id_unidad
                        WHERE pu.id_estudiante = ? AND u2.id_nivel = n.id AND u2.estado = 1"
                        . $this->filtroMaterias($materiasIds, 'u2') . ") AS unidades_completadas
                FROM nivel n
                WHERE n.estado = 1
                ORDER BY n.numero";
        return $this->db->fetchAll($sql, [$idEstudiante]);
    }

    /**
     * Determina si un nivel esta desbloqueado para un estudiante.
     * El nivel 1 siempre esta desbloqueado.
     * Niveles N>1 requieren que el nivel anterior tenga >= porcentaje_desbloqueo% completado.
     */
    public function nivelDesbloqueado($idEstudiante, $idNivel, $materiasIds = null)
    {
        $sql = "SELECT numero, porcentaje_desbloqueo FROM nivel WHERE id = ?";
        $nivel = $this->db->fetch($sql, [$idNivel]);
        if (!$nivel) return false;

        if ((int)$nivel['numero'] === 1) return true;

        $sqlAnterior = "SELECT id FROM nivel WHERE numero = ?";
        $nivelAnterior = $this->db->fetch($sqlAnterior, [(int)$nivel['numero'] - 1]);
        if (!$nivelAnterior) return true;

        $resumen = $this->resumenPorNivel($idEstudiante, $materiasIds);
        foreach ($resumen as $r) {
            if ((int)$r['nivel_id'] === (int)$nivelAnterior['id']) {
                $total = (int)$r['total_unidades'];
                if ($total === 0) return true;
                $porcentaje = ((int)$r['unidades_completadas'] / $total) * 100;
                return $porcentaje >= (int)$nivel['porcentaje_desbloqueo'];
            }
        }
        return false;
    }

    /**
     * Genera la clausula SQL para filtrar por materias, o '' si no se filtra.
     * $alias es el alias de la tabla unidad en la query.
     */
    private function filtroMaterias($materiasIds, $alias)
    {
        if ($materiasIds === null) {
            return '';
        }
        $materiasIds = array_values(array_map('intval', (array)$materiasIds));
        if (empty($materiasIds)) {
            return " AND $alias.id_materia IN (0)";
        }
        $materiasSql = implode(',', $materiasIds);
        return " AND $alias.id_materia IN ($materiasSql)";
    }
}
