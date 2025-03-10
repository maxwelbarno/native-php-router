<?php

namespace Model;

class Token
{
    private $id;
    private $token;
    private $expiresAt;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
