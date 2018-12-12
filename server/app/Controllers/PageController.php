<?php namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Kernel\ControllerAbstract;

use Helpers\Api;
use Helpers\ApiCache;
use Helpers\Helper;

class PageController extends ControllerAbstract
{
    public function privacy(Request $request, Response $response){
        $flashes = [];
        if (isset($_SESSION['flash'])) {
            $flashes = $_SESSION['flash'];

            unset($_SESSION['flash']);
        }

        return $this->render('privacypolicy.twig.php', [
            'flashes' => $flashes
        ]);
    }
}
