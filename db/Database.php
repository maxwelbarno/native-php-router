<?php

namespace DB;

use Controller\Controller;
use PDO;
use PDOException;

final class Database extends Controller
{
    private $host;
    private $username;
    private $password;
    private $db;
    public $pdo = null;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->db = $_ENV['DB_NAME'];
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8";
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(500);
            echo json_encode(["code" => 500, "message" => $e->getMessage()]);
            exit();
        }
    }

    public function query($sql)
    {
        $query = $this->pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryOne($stmnt)
    {
        $stmnt->execute();
        return $stmnt->fetch(PDO::FETCH_ASSOC);
    }

    public function bind($stmt, $parameter, $value, $return_type = PDO::PARAM_STR)
    {
        $stmt->bindValue($parameter, $value, $return_type);
    }
}
