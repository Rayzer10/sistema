<?php
class Materia extends Model
{
    protected $table = 'materia';

    public function conConteo()
    {
        $sql = "SELECT m.*,
                       (SELECT COUNT(*) FROM seccion_materia sm WHERE sm.id_materia = m.id) AS total_secciones
                FROM materia m
                ORDER BY m.nombre";
        return $this->db->fetchAll($sql);
    }

    public function listarMaterias()
    {
        $sql = "SELECT id, nombre FROM materia ORDER BY nombre";
        return $this->db->fetchAll($sql);
    }
}
