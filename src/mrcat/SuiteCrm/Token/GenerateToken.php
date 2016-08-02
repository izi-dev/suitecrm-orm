<?php namespace MrCat\SuiteCrm\Token;

use MrCat\SuiteCrm\Http\Api;

class GenerateToken
{

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     *
     * @return array
     */
    public static function attempt(array $credentials)
    {
        $api = Api::get()->addSession($credentials['user'], $credentials['password']);

        $payload = \JWTFactory::make([
            'id' => $api->getSession(),
        ]);

        $token = \JWTAuth::encode($payload);

        return $token->get();
    }

    /**
     * Decode token request header
     *
     * @return object
     */
    public static function getToken()
    {
        $jwt = \JWTAuth::getToken();

        if (!$jwt) {
            return null;
        }
        
        return \JWTAuth::decode($jwt)->get();
    }
}