<?php namespace App\Services;

use App\Kernel\ServiceInterface;

class NotAllowedService implements ServiceInterface
{

    /**
     * Service register name
     */
    public function name()
    {
        return 'notAllowedHandler';
    }

    /**
     * Register new service on dependency container
     */
    public function register()
    {
        return function ($c) {
            return function ($request, $response, $methods) use ($c) {
                $data = [
                    'msg' => 'Method not allowed. Must be one of: ' . implode(', ', $methods),
                    'status' => 405
                ];

                return $c['response']->withJson($data, 405)->withHeader('Allow', implode(', ', $methods));
            };
        };
    }
}
