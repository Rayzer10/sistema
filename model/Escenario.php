<?php
class Escenario extends Model
{
    protected $table = 'escenario';

    public function findCompleto($id)
    {
        $sql = "SELECT e.*, n.nombre AS nivel, n.numero AS nivel_numero
                FROM escenario e
                INNER JOIN nivel n ON n.id = e.id_nivel
                WHERE e.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function listarTodos()
    {
        $sql = "SELECT e.*, n.nombre AS nivel
                FROM escenario e
                INNER JOIN nivel n ON n.id = e.id_nivel
                WHERE e.estado = 1
                ORDER BY n.numero, e.id";
        return $this->db->fetchAll($sql);
    }
}
