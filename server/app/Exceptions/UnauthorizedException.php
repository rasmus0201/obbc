<?php

use Slim\Middleware\TokenAuthentication\UnauthorizedExceptionInterface;

if (!class_exists('UnauthorizedException')) {
    class UnauthorizedException extends \Exception implements UnauthorizedExceptionInterface{ }
}
