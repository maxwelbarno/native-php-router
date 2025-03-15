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
define("UNAUTHORIZED", "HTTP/1.1 401 Unauthorized");

class UserController extends Controller
{
    public function createUser()
    {
        try {
            $request_body = $this->request->getRequestBody();
            $user = new User(($request_body));
            $repository = new UserMapper();
            $res = $repository->save($user);
            $res ? response($this->response, "HTTP/1.1 201 Created", 201) :
            throw new CustomException("Error in Request Body");
        } catch (CustomException $e) {
            response($this->response, "HTTP/1.1 400 Bad Request", 400, $e->getMessage());
        }
    }

    public function getUser($id)
    {
        try {
            $repository = new UserMapper();
            $user = $repository->fetchOne($id);
            $keys = ["id","username", "password"];
            $user ? response($this->response, OK, 200, null, array_combine($keys, (array)$user)) :
            throw new CustomException("User with ID {$id} Not Found");
        } catch (CustomException $e) {
            response($this->response, NOT_FOUND, 404, $e->getMessage());
        }
    }

    public function getUsers()
    {
        try {
            $repository = new UserMapper();
            $auth = new Auth();
            if ($auth->authorize()) {
                try {
                    $users = $repository->fetchAll();
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
            } else {
                response($this->response, UNAUTHORIZED, 401, "Missing Authorization Header", null);
            }
        } catch (CustomException $e) {
            response($this->response, UNAUTHORIZED, 401, $e->getMessage());
        }
    }

    public function updateUser($id)
    {
        try {
            $request_body = $this->request->getRequestBody();
            $data = new User(($request_body));
            $repository = new UserMapper();
            $user = $repository->fetchOne($id);
            $user && $repository->update($data, $id) ? response($this->response, OK, 200) :
            throw new CustomException("User with ID {$id} Not Found");
        } catch (CustomException $e) {
            response($this->response, NOT_FOUND, 404, $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            $repository = new UserMapper();
            $user = $repository->fetchOne($id);
            $user && $repository->delete($id) ? response($this->response, OK, 200) :
            throw new CustomException("User with ID {$id} Not Found");
        } catch (CustomException $e) {
            response($this->response, NOT_FOUND, 404, $e->getMessage());
        }
    }
}
