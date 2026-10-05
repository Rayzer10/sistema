<?php
class ActividadEntrega extends Model
{
    protected $table = 'actividad_entrega';

    /**
     * Devuelve la entrega del estudiante sobre una actividad, creandola si no existe.
     */
    public function obtener($idActividad, $idEstudiante)
    {
        $sql = "SELECT * FROM actividad_entrega WHERE id_actividad = ? AND id_estudiante = ? LIMIT 1";
        $entrega = $this->db->fetch($sql, [$idActividad, $idEstudiante]);
        if ($entrega) return $entrega;

        $sql = "INSERT INTO actividad_entrega (id_actividad, id_estudiante, estado) VALUES (?, ?, 1)";
        $id = $this->db->insert($sql, [$idActividad, $idEstudiante]);
        return $this->db->fetch("SELECT * FROM actividad_entrega WHERE id = ?", [$id]);
    }

    /**
     * Mapa parte_id => respuesta de una entrega
     */
    public function respuestas($idEntrega)
    {
        $sql = "SELECT * FROM actividad_parte_respuesta WHERE id_entrega = ?";
        $rows = $this->db->fetchAll($sql, [$idEntrega]);
        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r['id_parte']] = $r;
        }
        return $out;
    }

    public function estaRespondida($idEntrega, $idParte)
    {
        $sql = "SELECT id FROM actividad_parte_respuesta WHERE id_entrega = ? AND id_parte = ? LIMIT 1";
        $row = $this->db->fetch($sql, [$idEntrega, $idParte]);
        return $row !== false && $row !== null;
    }

    /**
     * Registra el inicio del temporizador de una parte (si aun no existe) y
     * devuelve los segundos restantes. Null si la parte no tiene limite.
     */
    public function inicioParte($idEntrega, $idParte, $tiempoLimiteMin)
    {
        $total = (int)$tiempoLimiteMin * 60;
        if ($total <= 0) return null;

        $sql = "SELECT id, inicio_at FROM actividad_parte_respuesta WHERE id_entrega = ? AND id_parte = ? LIMIT 1";
        $row = $this->db->fetch($sql, [$idEntrega, $idParte]);

        if (!$row) {
            $this->db->execute(
                "INSERT INTO actividad_parte_respuesta (id_entrega, id_parte, respondida, inicio_at)
                 VALUES (?, ?, 0, NOW())",
                [$idEntrega, $idParte]
            );
            return $total;
        }

        if (empty($row['inicio_at'])) {
            $this->db->execute(
                "UPDATE actividad_parte_respuesta SET inicio_at = NOW() WHERE id = ?",
                [$row['id']]
            );
            return $total;
        }

        $restante = $total - (time() - strtotime($row['inicio_at']));
        return max(0, $restante);
    }

    /**
     * Segundos transcurridos desde el inicio del temporizador de una parte.
     * Null si no se inicio.
     */
    public function tiempoUsadoParte($idEntrega, $idParte)
    {
        $sql = "SELECT inicio_at FROM actividad_parte_respuesta WHERE id_entrega = ? AND id_parte = ? LIMIT 1";
        $row = $this->db->fetch($sql, [$idEntrega, $idParte]);
        if (!$row || empty($row['inicio_at'])) return null;
        return max(0, time() - strtotime($row['inicio_at']));
    }

    /**
     * Guarda el resultado de la evaluacion de una parte.
     * Si es la ultima parte activa, marca la entrega como completada y calcula
     * la calificacion final (promedio de las partes).
     */
    public function guardarRespuesta($idEntrega, $idParte, $puntaje, $tiempoUsadoSeg, $totalPartesActivas)
    {
        $sql = "INSERT INTO actividad_parte_respuesta (id_entrega, id_parte, puntaje, tiempo_usado_seg, respondida)
                VALUES (?, ?, ?, ?, 1)
                ON DUPLICATE KEY UPDATE puntaje = VALUES(puntaje),
                                        tiempo_usado_seg = VALUES(tiempo_usado_seg),
                                        respondida = 1";
        $this->db->execute($sql, [$idEntrega, $idParte, $puntaje, $tiempoUsadoSeg]);

        $respondidas = $this->db->fetch(
            "SELECT COUNT(*) AS total FROM actividad_parte_respuesta WHERE id_entrega = ? AND respondida = 1",
            [$idEntrega]
        );

        if ((int)$respondidas['total'] >= (int)$totalPartesActivas) {
            $prom = $this->calificacionPromedio($idEntrega);
            $this->db->execute(
                "UPDATE actividad_entrega SET estado = 2, calificacion = ? WHERE id = ?",
                [$prom, $idEntrega]
            );
        }
    }

    /**
     * Promedio del puntaje (0-100) de las partes respondidas
     */
    public function calificacionPromedio($idEntrega)
    {
        $sql = "SELECT COALESCE(AVG(puntaje), 0) AS promedio
                FROM actividad_parte_respuesta
                WHERE id_entrega = ? AND respondida = 1 AND puntaje IS NOT NULL";
        $row = $this->db->fetch($sql, [$idEntrega]);
        return round((float)($row['promedio'] ?? 0), 2);
    }

    /**
     * Entregas de una actividad (para el docente) con datos del estudiante
     */
    public function entregasDeActividad($idActividad)
    {
        $sql = "SELECT ae.*, u.nombre, u.correo,
                       (SELECT s.nombre
                        FROM seccion_actividad sa2
                        INNER JOIN estudiante_seccion es2 ON es2.id_seccion = sa2.id_seccion
                        INNER JOIN seccion s ON s.id = sa2.id_seccion
                        WHERE sa2.id_actividad = ae.id_actividad AND es2.id_estudiante = ae.id_estudiante
                        LIMIT 1) AS seccion_nombre,
                       (SELECT COUNT(*) FROM actividad_parte_respuesta apr
                        WHERE apr.id_entrega = ae.id AND apr.respondida = 1) AS partes_completadas,
                       (SELECT COUNT(*) FROM actividad_parte ap
                        WHERE ap.id_actividad = ae.id_actividad AND ap.estado = 1) AS total_partes
                FROM actividad_entrega ae
                INNER JOIN usuario u ON u.id = ae.id_estudiante
                WHERE ae.id_actividad = ?
                ORDER BY u.nombre";
        return $this->db->fetchAll($sql, [$idActividad]);
    }
}