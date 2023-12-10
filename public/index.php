<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use szolprog\API\DB;
use szolprog\API\TokenCreator;
use szolprog\API\Middleware\TokenAuthMiddleware;
use szolprog\API\Middleware\AuthLevelMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/SzolProg-Rest-uni');
$app->addBodyParsingMiddleware();
$app->add(new TokenAuthMiddleware('/SzolProg-Rest-uni'));


$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("No endpoint provided!");
    return $response;
});


//region users endpoints
$app->get('/users', function (Request $request, Response $response) {
    if($request->getAttribute('isAdmin') == 'true') {
        $db = new DB();
        $pdo = $db->connect();
        $statement = $pdo->prepare('SELECT * FROM user');
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    }
    $response->getBody()->write(json_encode('Authorization Level Error.',));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(401);
})->add(new AuthLevelMiddleware());

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

$app->post('/users/register', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare("INSERT INTO user(`username`,`password`,`last_name`,`token`,`is_admin`) VALUES (?,?,?,?,0)");
    $token = TokenCreator::generateToken();
    $statement->execute([$data['username'],md5($data['password']),$data['last_name'],$token]);
    $last_id=$pdo->lastInsertId();
    $statement = $pdo->prepare('SELECT id,username,last_name,is_admin,registration_date FROM user where id=?');
    $statement->execute([$last_id]);
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

$app->put('/users', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare("UPDATE user SET username = ?, last_name = ? WHERE id = ?");
    $statement->execute([$data['username'],$data['last_name'],$data['id']]);

    $statement = $pdo->prepare("SELECT id,username,last_name FROM user WHERE id = ?");
    $statement->execute($data['id']);
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

//endregion

//region drinks endpoints

//endregion

$app->run();