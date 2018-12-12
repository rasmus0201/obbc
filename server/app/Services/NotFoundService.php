<?php namespace App\Services;

use App\Kernel\ServiceInterface;

class NotFoundService implements ServiceInterface
{

    /**
     * Service register name
     */
    public function name()
    {
        return 'notFoundHandler';
    }

    /**
     * Register new service on dependency container
     */
    public function register()
    {
        return function ($c) {
            return function ($request, $response) use ($c) {
                $data = [
                    'msg' => 'Not found',
                    'status' => 404
                ];

                return $c['response']->withJson($data, 404);
            };
        };
    }
}
