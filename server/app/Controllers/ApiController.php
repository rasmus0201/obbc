<?php namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Kernel\ControllerAbstract;

use Tebru\AesEncryption\AesEncrypter as AesEncrypter;

use App\Helpers\Api;
use App\Helpers\ApiCache;
use App\Helpers\Helper;

class ApiController extends ControllerAbstract
{
    public function navigation(Request $request, Response $response) {
        $training = array(
            array(
                'id' => 0,
                'name' => 'Træning',
            ),
            array(
                'id' => 1,
                'name' => '  Holdtræning',
                'url' => 'http://www.obbc.dk/traening/holdtraening/',
            ),
            array(
                'id' => 2,
                'name' => '  Bodybike',
                'url' => 'http://www.obbc.dk/traening/bodybike/',
            ),
            array(
                'id' => 3,
                'name' => '  Crosstræning',
                'url' => 'http://www.obbc.dk/traening/crosstraining/',
            ),
            array(
                'id' => 4,
                'name' => '  Styrketræning',
                'url' => 'http://www.obbc.dk/traening/styrketraening/',
            ),
            array(
                'id' => 5,
                'name' => '  Styrkeløft',
                'url' => 'http://www.obbc.dk/traening/styrkeloeft/',
            ),
            array(
                'id' => 6,
                'name' => '  Vi hjælper dig',
                'url' => 'http://www.obbc.dk/traening/suppler-din-traening/',
            ),
        );

        $membership = array(
            array(
                'id' => 0,
                'name' => 'Medlemskab',
            ),
            array(
                'id' => 1,
                'name' => '  Medlemskab & priser',
                'url' => 'http://www.obbc.dk/medlemskab/medlemskaber-og-priser/',
            ),
            array(
                'id' => 2,
                'name' => '  Handelsbetingelser',
                'url' => 'http://www.obbc.dk/medlemskab/medlemskaber-og-priser/handelsbetingelser/',
            ),
            array(
                'id' => 3,
                'name' => '  Medlemsfordele',
                'url' => 'http://www.obbc.dk/medlemskab/medlemsfordele/',
            ),
        );

        $about = array(
            array(
                'id' => 0,
                'name' => 'Om OBBC',
            ),
            array(
                'id' => 1,
                'name' => '  Om foreningen',
                'url' => 'http://www.obbc.dk/om-obbc/foreningen/',
            ),
            array(
                'id' => 2,
                'name' => '  Åbningstider',
                'url' => 'http://www.obbc.dk/om-obbc/foreningen/aabningstider/',
            ),
            array(
                'id' => 3,
                'name' => '  Lokaler',
                'url' => 'http://www.obbc.dk/om-obbc/lokaler-og-galleri/',
            ),
            array(
                'id' => 3,
                'name' => '  Hvem er vi',
                'url' => 'http://www.obbc.dk/om-obbc/hvem-er-vi/',
            ),
            array(
                'id' => 4,
                'name' => '  Vedtægter for OBBC',
                'url' => 'http://www.obbc.dk/om-obbc/generelt/vedtaegter-for-obbc/',
            ),
        );

        $contact = array(
            array(
                'id' => 0,
                'name' => 'Kontakt',
            ),
            array(
                'id' => 1,
                'name' => '  Kontakt OBBC',
                'url' => 'http://www.obbc.dk/kontakt/',
            ),
        );

        $privacy = array(
            array(
                'id' => 1,
                'name' => 'Privatlivspolitik for OBBC App',
                'url' => 'http://165.227.174.67/obbc/privatlivspolitik',
            ),
        );

        return $response->withJson([
            'status'    => 200,
            'cached'    => false,
            'msg'       => 'Success',
            'data'      => array_merge($training, $membership, $about, $contact, $privacy)
        ]);
    }

    public function date(Request $request, Response $response){
        $datetime = Helper::date();

        return $response->withJson([
            'status'    => 200,
            'cached'    => false,
            'msg'       => 'Success',
            'data'      => [
                'timestamp' => $datetime->getTimestamp(),
                'datetime'  => $datetime->format('Y/m/d H:i:s'),
                'date'  => $datetime->format('Y/m/d'),
                'weekDay'  => (int)$datetime->format('w'),
            ]
        ], 200);
    }

    public function auth_jwt(Request $request, Response $response){
        $username = $request->getParam('username');
        $password = $request->getParam('password');

        if ( (empty($username)||empty($password)) || (!is_string($username)||!is_string($password)) ) {
            return $response->withJson(['status' => 401, 'msg' => 'Brugernavn og adgangskode skal bruges.'], 401);
        }

        //Check if user exists in DB
        $cache = new ApiCache($username, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

        $creds = base64_encode($username . ':' . $password);
        $api = new Api($creds);

        //Check if username/password is correct on flexybox
        $data = $api->auth();

        //Check if creds doesn't match
        if ($data['status'] != 200) {
            return $response->withJson($data, $data['status']);
        }

        $jwt_token = JWT::encode(['creds' => $username],
            env('APP_SECRET'), 'HS256');

        //Store user in DB
        $cache->CacheFile('auth.json', $data);

        //Go ahead and save this in cookie
        return $response->withJson($data, $data['status']);
    }

    public function auth(Request $request, Response $response) {
        //This token is the "plain text" credentials,
        //We are going to encrypt them using AES-256
        $token = rawurldecode($request->getParam('token'));

        if ( !Helper::tokenValid($token) ) {
            return $response->withJson(['status' => 401, 'msg' => 'Unauthorized'], 401);
        }

        $cache = new ApiCache($token, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

        if ($data = $cache->GetFile('auth.json', '48 hours')) {
            $data['cached'] = true;
            return $response->withJson($data, $data['status']);
        }

        $api = new Api($token);

        $data = $api->auth();
        $data['cached'] = false;

        if ($data['status'] == 200) {
            //Cache response

            //Disabled for now
            //$cache->CacheFile('auth.json', $data);
        }

        $encrypter = new AesEncrypter(env('APP_SECRET'));

        $data['data'] = [
            'credentials' => $encrypter->encrypt($token)
        ];

        return $response->withJson($data, $data['status']);
    }

    public function calendar(Request $request, Response $response, $timestamp = null) {
        try {
            //Make sure token didn't change
            if (Helper::tokenChanged($request->getParam('token'))) {
                return $response->withJson(['status' => 401, 'msg' => 'Unauthorized'], 401);
            }

            $token = Helper::decryptToken($_SESSION['token']);

            if (!isset($timestamp)) {
                $startOfWeek = Helper::startOfWeek();
            } else if (!is_string($timestamp) || !ctype_digit($timestamp)) {
                $startOfWeek = Helper::startOfWeek();
            } else {
                $startOfWeek = Helper::startOfWeek(date('m/d/Y', $timestamp));
            }

            $cache = new ApiCache($token, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

            $file = 'calendar-'.$startOfWeek->getTimestamp().'.json';
            if ($data = $cache->GetFile($file)) {
                $data['cached'] = true;
                return $response->withJson($data, $data['status']);
            }

            //Max 2 years back
            if ( $startOfWeek->format('Y') < (date('Y') - 2) ) {
                return $response->withJson(['status' => 404, 'msg' => 'Not found'], 404);
            } else if ( $startOfWeek->format('Y') > (date('Y') + 2)) { //max 2 years above
                return $response->withJson(['status' => 404, 'msg' => 'Not found'], 404);
            }

            $api = new Api($token);

            $res = [
                'status'    => 200,
                'cached'    => false,
                'msg'       => 'Success retrieving calendar for date: '.$startOfWeek->format('d/m/Y'),
                'data'      => $api->week($startOfWeek->format('m/d/Y'))
            ];

            //Cache response
            $cache->CacheFile($file, $res);

            return $response->withJson($res, $res['status']);
        } catch (\Exception $e) {
            return $response->withJson(['status' => 404, 'msg' => 'Not found'], 404);
        }
    }

    public function bookings(Request $request, Response $response) {
        //Make sure token didn't change
        if (Helper::tokenChanged($request->getParam('token'))) {
            return $response->withJson(['status' => 401, 'msg' => 'Unauthorized'], 401);
        }

        $token = Helper::decryptToken($_SESSION['token']);

        $api = new Api($token);

        $res = [
            'status'    => 200,
            'cached'    => false,
            'msg'       => 'Success',
            'data'      => []
        ];

        $cache = new ApiCache($token, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

        if ($data = $cache->GetFile('bookings.json')) {
            $data['cached'] = true;
            return $response->withJson($data, $data['status']);
        }

        $bookings = $api->bookings();

        if (!empty($bookings)) {
            $res['data'] = $bookings;
        }

        return $response->withJson($res, $res['status']);
    }

    public function book(Request $request, Response $response, $teamId) {
        //Make sure token didn't change
        if (Helper::tokenChanged($request->getParam('token'))) {
            return $response->withJson(['status' => 401, 'msg' => 'Unauthorized'], 401);
        }

        $token = Helper::decryptToken($_SESSION['token']);

        if (!is_string($teamId) || !ctype_digit($teamId)) {
            return $response->withJson(['status' => 404, 'msg' => 'Not found'], 404);
        }

        $cache = new ApiCache($token, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

        $cache->DeleteFile('bookings.json');
        $cache->DeleteFile('calendar-*.json');

        $api = new Api($token);

        $data = $api->book($teamId);
        $data['cached'] = false;

        $cache = new ApiCache($token, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

        //We can't "trust" the cached json file nor
        //the bookings, so just prune everything
        $deleted = $cache->DeleteUserDir();

        return $response->withJson($data, $data['status']);
    }

    public function leave(Request $request, Response $response, $bookingId) {
        //Make sure token didn't change
        if (Helper::tokenChanged($request->getParam('token'))) {
            return $response->withJson(['status' => 401, 'msg' => 'Unauthorized'], 401);
        }

        $token = Helper::decryptToken($_SESSION['token']);

        if (!is_string($bookingId) || !ctype_digit($bookingId)) {
            return $response->withJson(['status' => 404, 'msg' => 'Not found'], 404);
        }

        $api = new Api($token);

        $data = $api->leave($bookingId);
        $data['cached'] = false;

        //Check which week the booking is in and only delete that cache (calendar)
        $cache = new ApiCache($token, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

        //We can't "trust" the cached json file nor
        //the bookings, so just prune everything
        $deleted = $cache->DeleteUserDir();

        return $response->withJson($data, $data['status']);
    }

    public function delete(Request $request, Response $response){
        //Make sure token didn't change
        if (Helper::tokenChanged($request->getParam('token'))) {
            return $response->withJson(['status' => 401, 'msg' => 'Unauthorized'], 401);
        }

        $token = Helper::decryptToken($_SESSION['token']);

        $cache = new ApiCache($token, storage_path() . env('APP_API_CACHE', '/cache/api') . '/');

        //User requested deletion
        $deleted = $cache->DeleteUserDir();

        $res = [
            'status'    => $deleted ? 200 : 422,
            'cached'    => false,
            'msg'       => $deleted ? 'Success dine data blev slettet.': 'Fejl, prøv igen senere.',
        ];

        return $response->withJson($res, $res['status']);
    }
}
