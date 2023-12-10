<?php

namespace szolprog\API\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use szolprog\API\DB;
class AuthLevelMiddleware {
    public function __invoke(Request $request, RequestHandler $handler){

        $token = $request->getHeaderLine('Token');
        if ($token) {
            $db = new DB();
            $pdo = $db->connect();
            $statement = $pdo->prepare('SELECT is_admin FROM user WHERE token = ?');
            $statement->execute([$token]);
            if ($statement->fetch(\PDO::FETCH_ASSOC)['is_admin'] == 1) {
                return $handler->handle($request->withAttribute('isAdmin','true'));
            }
            else if(!is_null($request->getParsedBody())) {
                $target_user_id = $request->getParsedBody()['user_id'];
                if(isset($target_user_id)) {
                    $statement = $pdo->prepare('SELECT id FROM user WHERE token = ?');
                    $statement->execute([$token]);
                    if ($statement->fetch(\PDO::FETCH_ASSOC)['id'] == $target_user_id) {
                        return $handler->handle($request->withAttribute('isAdmin', 'false'));
                    }
                }
            }
        }
        $data = ['error' => "Authorization level error. You don't have permission to use this route"];
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(401);
    }

}