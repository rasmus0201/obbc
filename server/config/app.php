<?php

return [
    'displayErrorDetails' => env('APP_DEBUG', false),
    'determineRouteBeforeAppMiddleware' => true,
    'addContentLengthHeader' => false,

    'services' => require 'services.php',

    'middlewares' => require 'middlewares.php',

    'db' => require 'db.php',

    'twig' => require 'twig.php',


    /* Custom */
    'jwt' => [
        'secret' => env('APP_SECRET')
    ]
];
