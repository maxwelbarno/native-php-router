<?php

namespace DataMapper;

use Exceptions\CustomException;
use Model\User;
use Query\Query;

class UserMapper
{
    public $table;
    public $primaryKey;
    public $query;

    public function __construct()
    {
        $this->table = "users";
        $this->primaryKey = "id";
        $this->query = new Query($this->table, $this->primaryKey);
    }

    public function save(User $user)
    {
        $password = $user->getPassword();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $data = [
                "username" => $user->getUsername(),
                "password" => $hash
            ];
            return $this->query->create($data);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function fetchAll()
    {
        try {
            $list = array();
            $data = $this->query->findAll();
            foreach ($data as $row) {
                $user = new User($row);
                $list[] = $user;
            }
            return $list;
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function fetchOne($id)
    {
        try {
            $data = $this->query->findById($id);
            if ($data) {
                return new User($data);
            }
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function fetchByUsername($username)
    {
        try {
            $data = $this->query->findByUsername($username);
            if ($data) {
                return new User($data);
            }
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function update(User $user, $id)
    {
        $data = [
            "username" => $user->getUsername(),
            "password" => $user->getPassword(),
        ];
        return $this->query->update($data, $id);
    }

    public function delete($id)
    {
        return $this->query->delete($id);
    }
}
