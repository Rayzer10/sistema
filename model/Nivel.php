<?php
class Nivel extends Model
{
    protected $table = 'nivel';

    public function todos()
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 1 ORDER BY numero";
        return $this->db->fetchAll($sql);
    }

    public function conUnidadesPublicadas()
    {
        $sql = "SELECT n.*,
                       (SELECT COUNT(*) FROM unidad u WHERE u.id_nivel = n.id AND u.estado = 1) AS total_unidades
                FROM nivel n
                WHERE n.estado = 1
                ORDER BY n.numero";
        return $this->db->fetchAll($sql);
    }
}
