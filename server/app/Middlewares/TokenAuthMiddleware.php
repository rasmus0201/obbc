<?php namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Middleware\TokenAuthentication;
use App\Helpers\Api;
use App\Exceptions\UnauthorizedException;

use Tebru\AesEncryption\AesEncrypter as AesEncrypter;


class TokenAuthMiddleware {

    public static function middleware(){
        $authenticator = function(Request $request, TokenAuthentication $tokenAuth){

            # Search for token on header, parameter, cookie or attribute
            $token = rawurldecode($tokenAuth->findToken($request));

            $encrypter = new AesEncrypter(env('APP_SECRET'));
            $decrypted_token = $encrypter->decrypt($token);

            # Your method to make token validation
            $api = new Api($decrypted_token);

            $validate = $api->auth();
            if (!$validate) {
                throw new UnauthorizedException('Invalid token');
            }

            $_SESSION['token'] = $token;
        };

        $error = function(Request $request, Response $response,  TokenAuthentication $tokenAuth) {
            $output = [
                'msg'       => $tokenAuth->getResponseMessage(),
                'token'     => $tokenAuth->getResponseToken(),
                'status'    => 500,
            ];

            return $response->withJson($output, 500);
        };

        return new TokenAuthentication(array(
            'path'          => '/api/user',
            'passthrough'   => '/api/user/auth',
            'authenticator' => $authenticator,
            'error'         => $error,
            'parameter'     => 'token',
            'secure'        => env('SSL_ENABLED', false) === "1" ? true : false,
        ));
    }
}
