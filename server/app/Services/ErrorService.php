<?php namespace App\Services;

use App\Kernel\ServiceInterface;

class ErrorService implements ServiceInterface
{

    /**
     * Service register name
     */
    public function name()
    {
        return 'errorHandler';
    }

    /**
     * Register new service on dependency container
     */
    public function register()
    {
        return function ($c) {
            return function ($request, $response, $exception) use ($c) {
                $data = [
                    'msg' => 'Internal Server Error',
                    'status' => 500,
                    'exception' => $exception->getMessage(),
                ];

                return $c['response']->withJson($data, $data['status']);
            };
        };
    }
}
