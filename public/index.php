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

$app->post('/users/login', function (Request $request, Response $response) {
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

$app->post('/users/register', function (Request $request, Response $response) {
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

$app->put('/users', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare("UPDATE user SET username = ?, last_name = ? WHERE id = ?");
    $statement->execute([$data['username'],$data['last_name'],$data['user_id']]);

    $statement = $pdo->prepare("SELECT id,username,last_name FROM user WHERE id = ?");
    $statement->execute([$data['user_id']]);
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

//endregion

//region drinks endpoints

$app->get('/drinks', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    if($request->getAttribute('isAdmin') == 'true') {
        $statement = $pdo->prepare("SELECT * FROM drink_stock");
        $statement->execute();
    } else {
        $statement = $pdo->prepare("SELECT id,item_number,name,size,price,quantity,category_id FROM drink_stock WHERE owner_id = ?");
        $statement->execute([$data['user_id']]);
    }
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

$app->post('/drinks', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare("INSERT INTO drink_stock(`item_number`,`name`,`size`,`price`,`quantity`,`category_id`, `owner_id`) VALUES (?,?,?,?,?,?,?)");
    $statement->execute([$data['item_number'],$data['name'],$data['size'],$data['price'],$data['quantity'],$data['category_id'],$data['owner_id']]);
    $last_id=$pdo->lastInsertId();
    $statement = $pdo->prepare('SELECT item_number,name,size,price,quantity,category_id FROM drink_stock where id=?');
    $statement->execute([$last_id]);
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

$app->put('/drinks', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    if($request->getAttribute('isAdmin') == 'true') {
        $statement = $pdo->prepare("UPDATE drink_stock SET item_number = ?, name = ?, size = ?,price = ?,quantity = ?,category_id = ?,owner_id = ? WHERE id=?");
        $statement->execute([$data['item_number'],$data['name'],$data['size'],$data['price'],$data['quantity'],$data['category_id'],$data['owner_id'],$data['product_id']]);
    } else {
        $statement = $pdo->prepare("UPDATE drink_stock SET item_number = ?, name = ?, size = ?,price = ?,quantity = ?,category_id = ? WHERE id=?");
        $statement->execute([$data['item_number'],$data['name'],$data['size'],$data['price'],$data['quantity'],$data['category_id'],$data['product_id']]);
    }
    $statement = $pdo->prepare('SELECT item_number,name,size,price,quantity,category_id FROM drink_stock where id=?');
    $statement->execute([$data['product_id']]);
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

$app->delete('/drinks', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('DELETE FROM drink_stock WHERE id=?');
    if($statement->execute([$data['product_id']])){
        $response->getBody()->write(json_encode('Successfuly deleted.'));
    } else {
        $response->getBody()->write(json_encode('Something went wrong.'));
    }
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

//endregion

//region category endpoints
$app->get('/drink-category', function (Request $request, Response $response) {
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('SELECT * FROM drink_category');
    $statement->execute();
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

$app->post('/drink-category', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('INSERT INTO drink_category(`name`) values (?)');
    $statement->execute([$data['name']]);
    $last_id=$pdo->lastInsertId();
    $statement = $pdo->prepare('SELECT * FROM drink_category where id=?');
    $statement->execute([$last_id]);
    $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

$app->delete('/drink-category', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('DELETE FROM drink_category WHERE id = ?');
    if($statement->execute([$data['category_id']])){
        $response->getBody()->write(json_encode('Successfuly deleted.'));
    } else {
        $response->getBody()->write(json_encode('Something went wrong.'));
    }
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

$app->put('/drink-category', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $db = new DB();
    $pdo = $db->connect();
    $statement = $pdo->prepare('UPDATE drink_category SET name = ?  WHERE id = ?');
    $statement->execute([$data['name'],$data['category_id']]);
    $statement = $pdo->prepare('SELECT * FROM drink_category WHERE id = ?');
    $statement->execute([$data['category_id']]);
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
})->add(new AuthLevelMiddleware());

//endregion

$app->run();