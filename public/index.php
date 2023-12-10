<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/SzolProg-Rest-uni');


$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("valami");
    return $response;
});

$app->run();