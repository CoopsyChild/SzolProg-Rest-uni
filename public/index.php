<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use szolprog\API\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/SzolProg-Rest-uni');


$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("valami");
    return $response;
});

$app->get('/users', function (Request $request, Response $response) {
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('SELECT * FROM user');
    $statement->execute();
    $data = $statement->fetchAll(PFO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response;
});

$app->run();