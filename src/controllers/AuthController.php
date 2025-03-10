<?php

namespace AuthController;

use Auth\Auth;
use Controller\Controller;

use function Helpers\response;

class AuthController extends Controller
{
    public function login()
    {
        $request_body = $this->request->getRequestBody();
        $auth = new Auth();
        $access = $auth->authenticate($request_body['username'], $request_body['password']);
        if ($access) {
            response($this->response, "HTTP/1.1 200 OK", 200, null, $access);
        } else {
            response($this->response, "HTTP/1.1 401 Unauthorized", 401, "Invalid Login Credentials");
        }
    }
    public function refresh()
    {
        $request_body = $this->request->getRequestBody();
        $auth = new Auth();
        $tokens = $auth->getTokens($request_body['token']);
        if ($tokens) {
            response($this->response, "HTTP/1.1 200 OK", 200, null, $tokens);
        } else {
            response($this->response, "HTTP/1.1 401 Unauthorized", 401, "Invalid Refresh Token");
        }
    }
}
