<?php
class AsignacionEscenario extends Model
{
    protected $table = 'asignacion_escenario';

    public function listarPorEstudiante($idEstudiante)
    {
        $sql = "SELECT a.*,
                       e.titulo, e.contexto, e.problema, e.entregables_esperados, e.id_nivel,
                       n.nombre AS nivel,
                       en.id AS id_entrega,
                       en.archivo_nombre,
                       en.fecha_entrega,
                       ev.calificacion,
                       ev.retroalimentacion,
                       ev.fecha_evaluacion
                FROM asignacion_escenario a
                INNER JOIN escenario e ON e.id = a.id_escenario
                INNER JOIN nivel n ON n.id = e.id_nivel
                LEFT JOIN entrega_escenario en ON en.id_asignacion = a.id
                LEFT JOIN evaluacion_escenario ev ON ev.id_entrega = en.id
                WHERE a.id_estudiante = ?
                ORDER BY a.estado ASC, a.fecha_asignacion DESC";
        return $this->db->fetchAll($sql, [$idEstudiante]);
    }

    public function findCompleto($id)
    {
        $sql = "SELECT a.*,
                       e.titulo, e.contexto, e.problema, e.entregables_esperados, e.id_nivel,
                       n.nombre AS nivel, n.numero AS nivel_numero
                FROM asignacion_escenario a
                INNER JOIN escenario e ON e.id = a.id_escenario
                INNER JOIN nivel n ON n.id = e.id_nivel
                WHERE a.id = ?";
        $asig = $this->db->fetch($sql, [$id]);
        if (!$asig) return null;

        $sqlE = "SELECT * FROM entrega_escenario WHERE id_asignacion = ? LIMIT 1";
        $entrega = $this->db->fetch($sqlE, [$id]);
        $asig['entrega'] = $entrega;

        if ($entrega) {
            $sqlEva = "SELECT * FROM evaluacion_escenario WHERE id_entrega = ? LIMIT 1";
            $asig['evaluacion'] = $this->db->fetch($sqlEva, [$entrega['id']]);
        }

        return $asig;
    }
}
