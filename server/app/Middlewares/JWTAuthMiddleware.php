<?php namespace App\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;
use \Slim\Middleware\JwtAuthentication;
use App\Exceptions\UnauthorizedException;

class JWTAuthMiddleware {
    public static function middleware(){
        return new JwtAuthentication([
            'secret' => env('APP_SECRET', 'MUCH_WOW_SUCH_SECRET_PLS_CHANGE'),
            'path' => ['/api/user'],
            'passthrough' => ['/api/user/auth'],
            'cookie' => 'jwt_token',
            'attribute' => 'jwt_token_decoded',
            'secure' => false,
            'algorithm' => ['HS256'],
            'error' => function (Request $request, Response $response, $args) {
                $data = [
                    'status' => 'Error',
                    'message' => $args['message']
                ];

                return $response->withJson($data, 500);
            }
        ]);
    }
}
