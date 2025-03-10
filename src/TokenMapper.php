<?php

namespace DataMapper;

use Exceptions\CustomException;
use Model\Token;
use Query\TokenQuery;

class TokenMapper
{
    public $table;
    public $secretKey;
    public $query;

    public function __construct()
    {
        $this->table = "tokens";
        $this->secretKey = $_ENV["SECRET_KEY"];
        $this->query = new TokenQuery($this->table, $this->secretKey);
    }

    public function save(Token $token)
    {
        try {
            $data = [
                "token" => $token->getToken(),
                "expires_at" => $token->getExpiresAt()
            ];
            return $this->query->create($data);
        } catch (CustomException $e) {
            $e->render();
        }
    }

    public function delete($jwtToken)
    {
        $tk = new Token($jwtToken);
        $token = $tk->getToken();
        $hash = $this->query->findByToken($token);
        if ($hash) {
            return $this->query->delete($token);
        } else {
            return false;
        }
    }
}
