<?php

//Ugly but works
$GLOBALS['controllers'] = [
    'ApiController' => '\App\Controllers\ApiController',
    'PageController' => '\App\Controllers\PageController'
];

$app->group('/api', function() use ($app) {

    $app->get('/navigation', $GLOBALS['controllers']['ApiController'] . ':navigation');

    $app->get('/date', $GLOBALS['controllers']['ApiController'] . ':date');

    $app->group('/user', function () use ($app) {

        $app->get('/auth', $GLOBALS['controllers']['ApiController'] . ':auth');
        //$app->get('/auth', $GLOBALS['controllers']['ApiController'] . ':login');

        $app->get('/calendar[/{timestamp}]', $GLOBALS['controllers']['ApiController'] . ':calendar');

        $app->get('/bookings', $GLOBALS['controllers']['ApiController'] . ':bookings');

        $app->post('/bookings/book/{teamId}', $GLOBALS['controllers']['ApiController'] . ':book');

        $app->post('/bookings/leave/{bookingId}', $GLOBALS['controllers']['ApiController'] . ':leave');

        $app->post('/delete', $GLOBALS['controllers']['ApiController'] . ':delete');

    });

});

$app->get('/privatlivspolitik', $GLOBALS['controllers']['PageController'] . ':privacy');


$app->group('/obbc', function() use ($app) {
    //OBBC undersider
});
