<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use szolprog\API\DB;
use szolprog\API\Middleware\TokenAuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/SzolProg-Rest-uni');
$app->addBodyParsingMiddleware();


$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("valami");
    return $response;
});

$app->get('/users', function (Request $request, Response $response) {
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('SELECT * FROM user');
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

$app->post('/users/login', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('SELECT id,token,last_name,is_admin,registration_date FROM user where username = ? AND password = ?');
    $statement->execute([$data['username'],md5($data['password'],false)]);
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

$app->run();