<?php
// lib/BaseModel.php - Abstract Base Model
require_once __DIR__ . "/../config/database.php";
abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = "id";
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]); return $stmt->fetch();
    }
    public function findAll($conditions = [], $order = "", $limit = "") {
        $sql = "SELECT * FROM {$this->table}"; $params = [];
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $col => $val) { $where[] = "$col = ?"; $params[] = $val; }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        if ($order) $sql .= " ORDER BY $order";
        if ($limit) $sql .= " LIMIT $limit";
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function findOne($conditions) {
        $result = $this->findAll($conditions, "", "1");
        return $result ? $result[0] : null;
    }
    public function insert($data) {
        $cols = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($cols) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }
    public function update($id, $data) {
        $sets = implode(", ", array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $sets WHERE {$this->primaryKey} = ?");
        return $stmt->execute([...array_values($data), $id]);
    }
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) FROM {$this->table}"; $params = [];
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $col => $val) { $where[] = "$col = ?"; $params[] = $val; }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql); $stmt->execute($params); return $stmt;
    }
    public function beginTransaction() { return $this->db->beginTransaction(); }
    public function commit() { return $this->db->commit(); }
    public function rollback() { return $this->db->rollBack(); }
    public function lastInsertId() { return $this->db->lastInsertId(); }
}
