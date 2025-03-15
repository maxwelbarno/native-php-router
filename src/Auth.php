<?php

namespace Auth;

use DataMapper\TokenMapper;
use DataMapper\UserMapper;
use Exceptions\CustomException;
use JWT\Jwt;
use Model\Token;

class Auth
{
    public function authenticate($username, $password)
    {
        try {
            $data = new UserMapper();
            $user = $data->fetchByUsername($username);
            if ($user) {
                $hash = $user->getPassword();
                return password_verify($password, $hash) ? $this->generateTokens($user, $_ENV["SECRET_KEY"]) : null;
            }
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function authorize()
    {
        try {
            if (!isset($_SERVER["HTTP_AUTHORIZATION"])) {
                return false;
            } elseif (preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
                $jwt = new Jwt($_ENV["SECRET_KEY"]);
                $jwt->decode($matches[1]);
                return true;
            }
        } catch (CustomException $e) {
            $e->render();
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
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function generateTokens($user, $secretKey)
    {
        try {
            $tokenData = new TokenMapper();
            $payload = ["sub" => $user->getId(),"username" => $user->getUsername(),"exp" => time() + 600];
            $jwt = new Jwt($secretKey);
            $refresh_token = $jwt->encode(["sub" => $user->getId(), "exp" => time() + 43200]);
            $tokenData->save(new Token($refresh_token));
            return ["access_token" => $jwt->encode($payload),"refresh_token" => $refresh_token];
        } catch (CustomException $e) {
            $e->render();
        }
    }
}
