<?php

namespace Query;

use DB\Database;
use Exceptions\CustomException;
use PDO;

define("USERNAME", ":username");

class UserQuery
{
    protected $table;
    protected $primaryKey;
    private $db;

    public function __construct($table, $primaryKey)
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->db = new Database();
    }

    public function create(array $data): int
    {
        try {
            $sql = "INSERT INTO $this->table(username, password) VALUES(:username,:password)";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, USERNAME, $data['username']);
            $this->db->bind($stmt, ":password", $data['password']);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findAll()
    {
        try {
            return $this->db->query("SELECT * FROM $this->table");
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findById($id)
    {
        try {
            $sql = "SELECT * FROM $this->table WHERE $this->primaryKey=:id";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, ":id", $id, PDO::PARAM_INT);
            return $this->db->queryOne($stmt);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findByUsername($username)
    {
        try {
            $sql = "SELECT * FROM $this->table WHERE username=:username";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, USERNAME, $username);
            return $this->db->queryOne($stmt);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function update(array $data, string $id): int
    {
        try {
            $sql = "UPDATE $this->table SET username = :username, password = :password WHERE id = :id";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, ":id", $id);
            $this->db->bind($stmt, USERNAME, $data['username']);
            $this->db->bind($stmt, ":password", $data['password']);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function delete(string $id): int
    {
        try {
            $sql = "DELETE FROM $this->table  WHERE id = :id";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, ":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }
}
