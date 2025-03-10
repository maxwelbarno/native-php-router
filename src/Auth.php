<?php

namespace Auth;

use DataMapper\TokenMapper;
use DataMapper\UserMapper;
use JWT\Jwt;
use Model\Token;

class Auth
{
    public function authenticate($username, $password)
    {
        $data = new UserMapper();
        $user = $data->fetchByUsername($username);
        if ($user) {
            $hash = $user->getPassword();
            if (password_verify($password, $hash)) {
                return $this->generateTokens($user, $_ENV["SECRET_KEY"]);
            }
            return null;
        }
    }

    public function authorize()
    {
        if (preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
            $jwt = new Jwt($_ENV["SECRET_KEY"]);
            $jwt->decode($matches[1]);
            return true;
        }
    }

    public function getTokens($token)
    {
        try {
            $tokenData = new TokenMapper();
            if ($tokenData->delete($token)) {
                $jwt = new Jwt($_ENV["SECRET_KEY"]);
                $payload = $jwt->decode($token);
                $userId = $payload['sub'];
                $userData = new UserMapper();
                $user = $userData->fetchOne($userId);
                return $this->generateTokens($user, $_ENV["SECRET_KEY"]);
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            print_r($th->getMessage());
        }
    }

    public function generateTokens($user, $secretKey)
    {
        $tokenData = new TokenMapper();
        $payload = ["sub" => $user->getId(),"username" => $user->getUsername(),"exp" => time() + 60];
        $jwt = new Jwt($secretKey);
        $refresh_token = $jwt->encode(["sub" => $user->getId(), "exp" => time() + 43200]);
        $tokenData->save(new Token($refresh_token));
        return ["access_token" => $jwt->encode($payload),"refresh_token" => $refresh_token];
    }
}
