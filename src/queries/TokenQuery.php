<?php

namespace Query;

use DB\Database;
use Exceptions\CustomException;
use JWT\Jwt;
use PDO;

define("HASH", ":token_hash");
class TokenQuery
{
    protected $table;
    protected $secretKey;
    private $db;

    public function __construct($table, $secretKey)
    {
        $this->table = $table;
        $this->secretKey = $secretKey;
        $this->db = new Database();
    }

    public function create($data): int
    {
        $hash = hash_hmac("sha256", $data['token'], $this->secretKey);
        $jwt = new Jwt($this->secretKey);
        $payload = $jwt->decode($data['token']);
        try {
            $sql = "INSERT INTO $this->table(token_hash, expires_at) VALUES(:token_hash,:expires_at)";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, HASH, $hash);
            $this->db->bind($stmt, ":expires_at", $payload["exp"]);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findAll()
    {
        try {
            $sql = "SELECT * FROM $this->table";
            return $this->db->query($sql);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function findByToken($token)
    {
        $hash = hash_hmac("sha256", $token, $this->secretKey);
        try {
            $sql = "SELECT * FROM $this->table WHERE token_hash=:token_hash";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, HASH, $hash);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function delete($token)
    {
        $hash = hash_hmac("sha256", $token, $this->secretKey);
        try {
            $sql = "DELETE FROM $this->table  WHERE token_hash = :token_hash";
            $stmt = $this->db->pdo->prepare($sql);
            $this->db->bind($stmt, HASH, $hash);
            return $stmt->execute();
        } catch (CustomException $e) {
            $e->render();
        }
    }
}
