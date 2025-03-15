<?php

namespace DataMapper;

use Exceptions\CustomException;
use Model\User;
use Query\UserQuery;

class UserMapper
{
    public $table;
    public $primaryKey;
    public $query;

    public function __construct()
    {
        $this->table = "users";
        $this->primaryKey = "id";
        $this->query = new UserQuery($this->table, $this->primaryKey);
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
            return $data ? new User($data) : null;
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function fetchByUsername($username)
    {
        try {
            $data = $this->query->findByUsername($username);
            return $data ? new User($data) : null;
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function update(User $user, $id)
    {
        try {
            $data = [
                "username" => $user->getUsername(),
                "password" => password_hash($user->getPassword(), PASSWORD_DEFAULT)
            ];
            return $this->query->update($data, $id);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function delete($id)
    {
        try {
            return $this->query->delete($id);
        } catch (CustomException $e) {
            $e->render();
        }
    }
}
