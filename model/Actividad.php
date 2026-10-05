<?php
class Actividad extends Model
{
    protected $table = 'actividad';

    // =====================================================================
    // CRUD de actividades (docente)
    // =====================================================================

    public function listarPorMateria($idMateria)
    {
        $sql = "SELECT a.*, m.nombre AS materia,
                       n.nombre AS nivel,
                       u.titulo AS unidad,
                       (SELECT COUNT(*) FROM actividad_parte ap WHERE ap.id_actividad = a.id AND ap.estado = 1) AS total_partes,
                       (SELECT COUNT(*) FROM seccion_actividad sa WHERE sa.id_actividad = a.id) AS total_secciones,
                       (SELECT COUNT(*) FROM actividad_entrega ae WHERE ae.id_actividad = a.id) AS total_entregas
                FROM actividad a
                INNER JOIN materia m ON m.id = a.id_materia
                INNER JOIN unidad u ON u.id = a.id_unidad
                LEFT JOIN nivel n ON n.id = a.id_nivel
                WHERE a.id_materia = ?
                ORDER BY a.created_at DESC";
        return $this->db->fetchAll($sql, [$idMateria]);
    }

    public function findCompleta($id)
    {
        $sql = "SELECT a.*, m.nombre AS materia, n.nombre AS nivel, u.titulo AS unidad
                FROM actividad a
                INNER JOIN materia m ON m.id = a.id_materia
                INNER JOIN unidad u ON u.id = a.id_unidad
                LEFT JOIN nivel n ON n.id = a.id_nivel
                WHERE a.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function secciones($idActividad)
    {
        $sql = "SELECT s.id, s.nombre
                FROM seccion_actividad sa
                INNER JOIN seccion s ON s.id = sa.id_seccion
                WHERE sa.id_actividad = ?
                ORDER BY s.nombre";
        return $this->db->fetchAll($sql, [$idActividad]);
    }

    public function asignarSecciones($idActividad, array $idsSecciones)
    {
        $this->db->execute("DELETE FROM seccion_actividad WHERE id_actividad = ?", [$idActividad]);
        $ok = 0;
        foreach ($idsSecciones as $idS) {
            $idS = (int)$idS;
            if ($idS <= 0) continue;
            $this->db->execute("INSERT IGNORE INTO seccion_actividad (id_actividad, id_seccion) VALUES (?, ?)", [$idActividad, $idS]);
            $ok++;
        }
        return $ok;
    }

    // =====================================================================
    // Partes
    // =====================================================================

    public function partes($idActividad)
    {
        $sql = "SELECT ap.*,
                       (SELECT COUNT(*) FROM actividad_parte_pregunta p WHERE p.id_parte = ap.id) AS total_preguntas,
                       (SELECT COALESCE(SUM(p.puntos), 0) FROM actividad_parte_pregunta p WHERE p.id_parte = ap.id) AS total_puntos
                FROM actividad_parte ap
                WHERE ap.id_actividad = ?
                ORDER BY ap.orden, ap.id";
        return $this->db->fetchAll($sql, [$idActividad]);
    }

    public function parteFind($id)
    {
        $sql = "SELECT * FROM actividad_parte WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function parteGuardar($idActividad, $data, $id = null)
    {
        $data['id_actividad'] = $idActividad;
        if ($id) {
            $this->db->execute(
                "UPDATE actividad_parte SET titulo = ?, contenido_html = ?, tiempo_limite_min = ?, estado = ?, orden = ? WHERE id = ?",
                [$data['titulo'], $data['contenido_html'], $data['tiempo_limite_min'], $data['estado'], $data['orden'], $id]
            );
            return $id;
        }
        $sql = "INSERT INTO actividad_parte (id_actividad, orden, titulo, contenido_html, tiempo_limite_min, estado)
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->db->insert($sql, [
            $idActividad, $data['orden'], $data['titulo'], $data['contenido_html'], $data['tiempo_limite_min'], $data['estado']
        ]);
    }

    public function parteEliminar($id)
    {
        $this->db->execute("DELETE FROM actividad_parte WHERE id = ?", [$id]);
    }

    public function siguienteOrdenParte($idActividad)
    {
        $sql = "SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente FROM actividad_parte WHERE id_actividad = ?";
        $row = $this->db->fetch($sql, [$idActividad]);
        return (int)($row['siguiente'] ?? 1);
    }

    // =====================================================================
    // Preguntas y opciones
    // =====================================================================

    public function preguntasDeParte($idParte)
    {
        $sql = "SELECT * FROM actividad_parte_pregunta WHERE id_parte = ? ORDER BY orden, id";
        $preguntas = $this->db->fetchAll($sql, [$idParte]);
        foreach ($preguntas as &$p) {
            $p['opciones'] = $this->db->fetchAll(
                "SELECT * FROM actividad_parte_opcion WHERE id_pregunta = ? ORDER BY orden, id",
                [$p['id']]
            );
        }
        return $preguntas;
    }

    public function preguntaFind($id)
    {
        $sql = "SELECT * FROM actividad_parte_pregunta WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function preguntaGuardar($idParte, $data, $id = null)
    {
        if ($id) {
            $this->db->execute(
                "UPDATE actividad_parte_pregunta SET enunciado = ?, orden = ?, puntos = ? WHERE id = ?",
                [$data['enunciado'], $data['orden'], $data['puntos'], $id]
            );
            return $id;
        }
        $sql = "INSERT INTO actividad_parte_pregunta (id_parte, enunciado, orden, puntos) VALUES (?, ?, ?, ?)";
        return $this->db->insert($sql, [$idParte, $data['enunciado'], $data['orden'], $data['puntos']]);
    }

    public function preguntaEliminar($id)
    {
        $this->db->execute("DELETE FROM actividad_parte_pregunta WHERE id = ?", [$id]);
    }

    public function siguienteOrdenPregunta($idParte)
    {
        $sql = "SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente FROM actividad_parte_pregunta WHERE id_parte = ?";
        $row = $this->db->fetch($sql, [$idParte]);
        return (int)($row['siguiente'] ?? 1);
    }

    public function opcionesGuardar($idPregunta, array $opciones)
    {
        $this->db->execute("DELETE FROM actividad_parte_opcion WHERE id_pregunta = ?", [$idPregunta]);
        $orden = 1;
        foreach ($opciones as $op) {
            $texto = trim($op['texto'] ?? '');
            if ($texto === '') continue;
            $correcta = !empty($op['correcta']) ? 1 : 0;
            $retro = trim($op['retroalimentacion'] ?? '') ?: null;
            $this->db->execute(
                "INSERT INTO actividad_parte_opcion (id_pregunta, texto, es_correcta, orden, retroalimentacion)
                 VALUES (?, ?, ?, ?, ?)",
                [$idPregunta, $texto, $correcta, $orden, $retro]
            );
            $orden++;
        }
    }

    // =====================================================================
    // Listados para el estudiante
    // =====================================================================

    /**
     * Actividades publicadas de una materia asignadas a las secciones del estudiante
     */
    public function listarParaEstudiante($idEstudiante, $idMateria)
    {
        $sql = "SELECT a.*, m.nombre AS materia,
                       (SELECT COUNT(*) FROM actividad_parte ap WHERE ap.id_actividad = a.id AND ap.estado = 1) AS total_partes
                FROM seccion_actividad sa
                INNER JOIN actividad a ON a.id = sa.id_actividad
                INNER JOIN materia m ON m.id = a.id_materia
                INNER JOIN estudiante_seccion es ON es.id_seccion = sa.id_seccion
                WHERE es.id_estudiante = ? AND a.id_materia = ? AND a.estado = 1
                GROUP BY a.id
                ORDER BY a.created_at DESC";
        return $this->db->fetchAll($sql, [$idEstudiante, $idMateria]);
    }

    /**
     * Verifica que una actividad este asignada a alguna seccion del estudiante
     */
    public function estudianteTieneAcceso($idEstudiante, $idActividad)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM seccion_actividad sa
                INNER JOIN estudiante_seccion es ON es.id_seccion = sa.id_seccion
                WHERE sa.id_actividad = ? AND es.id_estudiante = ?";
        $row = $this->db->fetch($sql, [$idActividad, $idEstudiante]);
        return (int)($row['total'] ?? 0) > 0;
    }
}