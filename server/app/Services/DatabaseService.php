<?php namespace App\Services;

use App\Kernel\ServiceInterface;

class DatabaseService implements ServiceInterface
{

    /**
     * Service register name
     */
    public function name()
    {
        return 'db';
    }

    /**
     * Register new service on dependency container
     */
    public function register()
    {
        return function ($container) {
            $db = $container->settings['db'];

            $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['db'], $db['user'], $db['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            unset($container);

            return $pdo;
        };
    }
}
