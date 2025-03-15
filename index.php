<?php

namespace App;

require_once 'vendor/autoload.php';

use AuthController\AuthController;
use Dotenv\Dotenv;
use HomeController\HomeController;
use Http\Request;
use Http\Response;
use Router\Router;
use UserController\UserController;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$request = new Request();
$response = new Response();
$response->setHeader('Content-Type: application/json; charset=UTF-8');

$router = new Router($request->getUrl(), $request->getHttpMethod());
$router->get("/", [HomeController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'getUser']);
$router->get('/users', [UserController::class, 'getUsers']);
$router->post('/users/register', [UserController::class, 'createUser']);
$router->put('/users/{id}', [UserController::class, 'updateUser']);
$router->delete('/users/{id}', [UserController::class, 'deleteUser']);
$router->post('/auth/login', [AuthController::class, 'login']);
$router->post('/auth/refresh', [AuthController::class, 'refresh']);


$router->dispatch();
$response->render();
