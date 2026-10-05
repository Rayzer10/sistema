<?php
class IntentoDinamica extends Model
{
    protected $table = 'intento_dinamica';

    public function iniciar($idEstudiante, $idDinamica, $totalPreguntas)
    {
        $sql = "INSERT INTO intento_dinamica (id_estudiante, id_dinamica, total_preguntas) VALUES (?, ?, ?)";
        $this->db->execute($sql, [$idEstudiante, $idDinamica, $totalPreguntas]);
        return (int)$this->db->getConnection()->lastInsertId();
    }

    public function registrarRespuesta($idIntento, $idPregunta, $idOpcion, $correcta)
    {
        $sql = "INSERT INTO respuesta_intento (id_intento, id_pregunta, id_opcion, correcta) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE id_opcion = VALUES(id_opcion), correcta = VALUES(correcta)";
        return $this->db->execute($sql, [$idIntento, $idPregunta, $idOpcion, $correcta ? 1 : 0]);
    }

    public function finalizar($idIntento)
    {
        $sqlCorrectas = "SELECT COUNT(*) AS total FROM respuesta_intento WHERE id_intento = ? AND correcta = 1";
        $row = $this->db->fetch($sqlCorrectas, [$idIntento]);
        $correctas = (int)($row['total'] ?? 0);

        $sqlIntent = "SELECT total_preguntas FROM intento_dinamica WHERE id = ?";
        $intento = $this->db->fetch($sqlIntent, [$idIntento]);
        $total = (int)($intento['total_preguntas'] ?? 0);

        $calificacion = $total > 0 ? round(($correctas / $total) * 100, 2) : 0;

        $sqlUpd = "UPDATE intento_dinamica
                    SET fecha_fin = NOW(),
                        total_correctas = ?,
                        calificacion = ?
                    WHERE id = ?";
        $this->db->execute($sqlUpd, [$correctas, $calificacion, $idIntento]);

        return ['correctas' => $correctas, 'total' => $total, 'calificacion' => $calificacion];
    }

    public function findCompleto($id)
    {
        $sql = "SELECT i.*, d.titulo AS dinamica_titulo, n.nombre AS nivel
                FROM intento_dinamica i
                INNER JOIN dinamica d ON d.id = i.id_dinamica
                INNER JOIN nivel n ON n.id = d.id_nivel
                WHERE i.id = ?";
        $intento = $this->db->fetch($sql, [$id]);
        if (!$intento) return null;

        $sqlR = "SELECT r.*, p.enunciado, o.texto AS opcion_texto, o.es_correcta,
                        o.retroalimentacion
                FROM respuesta_intento r
                INNER JOIN pregunta_dinamica p ON p.id = r.id_pregunta
                INNER JOIN opcion_respuesta o ON o.id = r.id_opcion
                WHERE r.id_intento = ?
                ORDER BY p.orden, p.id";
        $intento['respuestas'] = $this->db->fetchAll($sqlR, [$id]);

        return $intento;
    }

    public function intentoEnCurso($idEstudiante, $idDinamica)
    {
        $sql = "SELECT * FROM intento_dinamica
                WHERE id_estudiante = ? AND id_dinamica = ? AND fecha_fin IS NULL
                ORDER BY fecha_inicio DESC LIMIT 1";
        return $this->db->fetch($sql, [$idEstudiante, $idDinamica]);
    }
}
