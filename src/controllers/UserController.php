<?php

namespace UserController;

use Auth\Auth;
use Controller\Controller;
use DataMapper\UserMapper;
use Exceptions\CustomException;
use Model\User;

use function Helpers\response;

define("OK", "HTTP/1.1 200 OK");
define("NOT_FOUND", "HTTP/1.1 404 Not Found");

class UserController extends Controller
{
    public function createUser()
    {
        try {
            $request_body = $this->request->getRequestBody();
            $user = new User(($request_body));
            $data = new UserMapper();
            $res = $data->save($user);
            if ($res) {
                response($this->response, "HTTP/1.1 201 Created", 201);
            } else {
                throw new CustomException("Error in Request Body");
            }
        } catch (CustomException $e) {
            response($this->response, "HTTP/1.1 400 Bad Request", 400, $e->getMessage());
        }
    }

    public function getUser($id)
    {
        try {
            $data = new UserMapper();
            $user = $data->fetchOne($id);
            if ($user) {
                $data = array_combine(["id","username", "password"], (array)$user);
                response($this->response, OK, 200, null, $data);
            } else {
                throw new CustomException("User with ID {$id} Not Found");
            }
        } catch (CustomException $e) {
            response($this->response, NOT_FOUND, 404, $e->getMessage());
        }
    }

    public function getUsers()
    {
        try {
            $data = new UserMapper();
            $auth = new Auth();
            if ($auth->authorize()) {
                try {
                    $users = $data->fetchAll();
                    if ($users) {
                        $list = [];
                        foreach ($users as $user) {
                            $list[] = array_combine(["id","username", "password"], (array)$user);
                        }
                        response($this->response, OK, 200, null, $list);
                    } else {
                        throw new CustomException("No User Found");
                    }
                } catch (CustomException $e) {
                    response($this->response, NOT_FOUND, 404, $e->getMessage());
                }
            }
        } catch (CustomException $e) {
            response($this->response, "401 Unauthorized", 401, $e->getMessage());
        }
    }

    public function updateUser($id)
    {
        try {
            $request_body = $this->request->getRequestBody();
            $user = new User(($request_body));
            $data = new UserMapper();
            if ($data->fetchOne($id)) {
                $data->update($user, $id);
                response($this->response, OK, 200);
            } else {
                throw new CustomException("User with ID {$id} Not Found");
            }
        } catch (CustomException $e) {
            response($this->response, NOT_FOUND, 404, $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            $data = new UserMapper();
            if ($data->fetchOne($id)) {
                $data->delete($id);
                response($this->response, OK, 200);
            } else {
                throw new CustomException("User with ID {$id} Not Found");
            }
        } catch (CustomException $e) {
            response($this->response, NOT_FOUND, 404, $e->getMessage());
        }
    }
}
