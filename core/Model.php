<?php
require_once __DIR__ . '/../model/Database.php';

class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function where($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchAll($sql, [$value]);
    }

    public function whereFirst($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1";
        return $this->db->fetch($sql, [$value]);
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        return $this->db->insert($sql, array_values($data));
    }

    public function update($id, $data)
    {
        $sets = implode(', ', array_map(function($col) {
            return "{$col} = ?";
        }, array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$sets} WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;
        return $this->db->execute($sql, $params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}