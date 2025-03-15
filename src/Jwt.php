<?php

namespace JWT;

use Exceptions\CustomException;

class Jwt
{
    public function __construct(private string $secretKey)
    {
    }

    public function encode(array $payload): string
    {
        $header = json_encode([
        "alg" => "HS256",
        "typ" => "JWT"
        ]);
        $header = $this->base64UrlEncode($header);
        $payload = json_encode($payload);
        $payload = $this->base64UrlEncode($payload);
        $signature = hash_hmac("sha256", $header . "." . $payload, $this->secretKey, true);
        $signature = $this->base64UrlEncode($signature);
        return $header . "." . $payload . "." . $signature;
    }

    public function decode($token)
    {
        preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/", $token, $matches) !== 1 ?
        throw new CustomException("Invalid Token Format") : null;

        $signature = hash_hmac("sha256", $matches["header"] . "." . $matches["payload"], $this->secretKey, true);
        $signatureFromToken = $this->base64UrlDecode($matches["signature"]);
        !hash_equals($signature, $signatureFromToken) ? throw new CustomException("Invalid Signature") : null;

        $payload = json_decode($this->base64UrlDecode($matches["payload"]), true);
        return $payload['exp'] < time() ? throw new CustomException("Token Expired") : $payload;
    }

    private function base64UrlEncode(string $text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private function base64UrlDecode(string $text): string
    {
        return base64_decode(
            str_replace(
                ["-", "_"],
                ["+", "/"],
                $text
            )
        );
    }
}
