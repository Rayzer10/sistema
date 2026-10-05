<?php
class Periodo extends Model
{
    protected $table = 'periodo_academico';

    public function listarActivos()
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 1 ORDER BY fecha_inicio DESC";
        return $this->db->fetchAll($sql);
    }

    public function conSecciones($id)
    {
        $sql = "SELECT COUNT(*) AS total FROM seccion WHERE id_periodo = ?";
        $row = $this->db->fetch($sql, [$id]);
        return (int)($row['total'] ?? 0);
    }

    public function periodoActivo()
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 1 ORDER BY fecha_inicio DESC LIMIT 1";
        return $this->db->fetch($sql);
    }
}
