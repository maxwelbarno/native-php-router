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
    private $conn;

    public function __construct($table, $primaryKey)
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function create(array $data): int
    {
        try {
            $sql = "INSERT INTO $this->table(username, password) VALUES(:username,:password)";
            $stmt = $this->conn->prepare($sql);
            $this->bind($stmt, USERNAME, $data['username']);
            $this->bind($stmt, ":password", $data['password']);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findAll()
    {
        try {
            $sql = "SELECT * FROM $this->table";
            return $this->query($sql);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findById($id)
    {
        try {
            $sql = "SELECT * FROM $this->table WHERE $this->primaryKey=:id";
            $stmt = $this->conn->prepare($sql);
            $this->bind($stmt, ":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findByUsername($username)
    {
        try {
            $sql = "SELECT * FROM $this->table WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $this->bind($stmt, USERNAME, $username);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function update(array $data, string $id): int
    {
        try {
            $sql = "UPDATE $this->table SET username = :username, password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $this->bind($stmt, ":id", $id);
            $this->bind($stmt, USERNAME, $data['username']);
            $this->bind($stmt, ":password", $data['password']);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function delete(string $id): int
    {
        try {
            $sql = "DELETE FROM $this->table  WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $this->bind($stmt, ":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }

    private function query($sql)
    {
        $query = $this->conn->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function bind($stmt, $parameter, $value, $return_type = PDO::PARAM_STR)
    {
        $stmt->bindValue($parameter, $value, $return_type);
    }
}
