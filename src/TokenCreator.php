<?php

namespace szolprog\API;

class TokenCreator
{
    public static function generateToken()
    {
        do {
            $db = new DB();
            $pdo = $db->connect();
            $statement = $pdo->prepare('SELECT token FROM user');
            $statement->execute();
            $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $length = 200;
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $token = '';

            for ($i = 0; $i < $length; $i++) {
                $token .= $characters[rand(0, $charactersLength - 1)];
            }
        } while(in_array($token,$data));

        return $token;
    }
}