<?php namespace App\Helpers;

use Tebru\AesEncryption\AesEncrypter as AesEncrypter;

class Helper
{
    public static function date($date = null){
        if ($date instanceof \DateTime) {
            $date = clone $date;
        } else if (!$date) {
            $date = new \DateTime();
        } else {
            try {
                $date = new \DateTime($date);
            } catch (Exception $e) {
                $date = new \DateTime();
            }
        }

        $date->setTime(0, 0, 0);

        return $date;
    }

    public static function startOfWeek($date = null){
        if ($date instanceof \DateTime) {
            $date = clone $date;
        } else if (!$date) {
            $date = new \DateTime();
        } else {
            $date = new \DateTime($date);
        }

        $date->setTime(0, 0, 0);

        if ($date->format('N') == 1) {
            // If the date is already a Monday, return it as-is
            return $date;
        } else {
            // Otherwise, return the date of the nearest Monday in the past
            // This includes Sunday in the previous week instead of it being the start of a new week
            return $date->modify('last monday');
        }
    }

    public static function dump(){
        echo '<pre>';
        foreach (func_get_args() as $arg) {
            var_dump($arg);
        }
        echo '</pre>';
    }

    public static function tokenValid($token){
        return ! (is_null($token)||!is_string($token)||count(explode(':', base64_decode($token))) !== 2);
    }

    public static function tokenChanged($newToken){
      return rawurldecode($newToken) !== $_SESSION['token'];
    }

    public static function decryptToken($token){
      $encrypter = new AesEncrypter(env('APP_SECRET'));
      return $encrypter->decrypt($token);
    }

    public static function hash($token){
        return self::serialize(password_hash($token, PASSWORD_BCRYPT, ['cost' => 12]));
    }

    public static function verify($dir, $token){
        return password_verify($token, self::unserialize($dir));
    }

    public static function serialize($s) {
        return str_replace(array('+', '/'), array('-', '_'), $s);
    }

    public static function unserialize($s) {
        return str_replace(array('-', '_'), array('+', '/'), $s);
    }
}
