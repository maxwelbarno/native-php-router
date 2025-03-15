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
        $access ? response($this->response, "HTTP/1.1 200 OK", 200, null, $access) :
        response($this->response, "HTTP/1.1 401 Unauthorized", 401, "Invalid Login Credentials");
    }
    public function refresh()
    {
        $request_body = $this->request->getRequestBody();
        $auth = new Auth();
        $tokens = $auth->getTokens($request_body['token']);
        $tokens ? response($this->response, "HTTP/1.1 200 OK", 200, null, $tokens) :
         response($this->response, "HTTP/1.1 401 Unauthorized", 401, "Invalid Refresh Token");
    }
}
