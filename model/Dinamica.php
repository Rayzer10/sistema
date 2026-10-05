<?php
class Dinamica extends Model
{
    protected $table = 'dinamica';

    public function conPreguntas($id)
    {
        $sql = "SELECT d.*, n.nombre AS nivel, n.numero AS nivel_numero
                FROM dinamica d
                INNER JOIN nivel n ON n.id = d.id_nivel
                WHERE d.id = ?";
        $dinamica = $this->db->fetch($sql, [$id]);
        if (!$dinamica) return null;

        $sqlP = "SELECT * FROM pregunta_dinamica WHERE id_dinamica = ? ORDER BY orden, id";
        $preguntas = $this->db->fetchAll($sqlP, [$id]);

        foreach ($preguntas as &$p) {
            $sqlO = "SELECT * FROM opcion_respuesta WHERE id_pregunta = ? ORDER BY orden, id";
            $p['opciones'] = $this->db->fetchAll($sqlO, [$p['id']]);
        }

        $dinamica['preguntas'] = $preguntas;
        return $dinamica;
    }

    /**
     * Lista dinamicas publicadas de un nivel
     */
    public function listarPorNivel($idNivel)
    {
        $sql = "SELECT d.*, n.nombre AS nivel
                FROM dinamica d
                INNER JOIN nivel n ON n.id = d.id_nivel
                WHERE d.id_nivel = ? AND d.estado = 1
                ORDER BY d.id";
        return $this->db->fetchAll($sql, [$idNivel]);
    }

    /**
     * Devuelve intentos previos del estudiante sobre cada dinamica
     */
    public function resumenIntentosEstudiante($idEstudiante, array $idsDinamicas = [])
    {
        if (empty($idsDinamicas)) return [];
        $placeholders = implode(',', array_fill(0, count($idsDinamicas), '?'));
        $sql = "SELECT id_dinamica, MAX(calificacion) AS mejor_calificacion,
                       COUNT(*) AS total_intentos,
                       MAX(fecha_fin) AS ultimo_intento
                FROM intento_dinamica
                WHERE id_estudiante = ? AND id_dinamica IN ($placeholders)
                GROUP BY id_dinamica";
        $params = array_merge([$idEstudiante], $idsDinamicas);
        $rows = $this->db->fetchAll($sql, $params);
        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r['id_dinamica']] = $r;
        }
        return $out;
    }

    /**
     * Mejor intento de un estudiante en una dinamica
     */
    public function mejorIntento($idEstudiante, $idDinamica)
    {
        $sql = "SELECT * FROM intento_dinamica
                WHERE id_estudiante = ? AND id_dinamica = ? AND fecha_fin IS NOT NULL
                ORDER BY calificacion DESC, fecha_fin DESC
                LIMIT 1";
        return $this->db->fetch($sql, [$idEstudiante, $idDinamica]);
    }

    /**
     * Listado de dinamicas para estudiante: incluye intentos previos y bloqueo por nivel
     */
    public function listarParaEstudiante($idEstudiante, $progresoModel)
    {
        $sql = "SELECT d.*, n.nombre AS nivel, n.numero AS nivel_numero, n.porcentaje_desbloqueo
                FROM dinamica d
                INNER JOIN nivel n ON n.id = d.id_nivel
                WHERE d.estado = 1
                ORDER BY n.numero, d.id";
        $dinamicas = $this->db->fetchAll($sql);

        $ids = array_map(fn($d) => (int)$d['id'], $dinamicas);
        $intentos = $this->resumenIntentosEstudiante($idEstudiante, $ids);

        foreach ($dinamicas as &$d) {
            $d['desbloqueado'] = $progresoModel->nivelDesbloqueado($idEstudiante, $d['id_nivel']);
            $d['intentos'] = $intentos[(int)$d['id']] ?? null;
        }

        return $dinamicas;
    }
}
