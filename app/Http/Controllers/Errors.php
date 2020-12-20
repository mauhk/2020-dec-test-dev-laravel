<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class Errors extends Model
{
    private static $errors = [
        "10001" => "Unauthenticated",
        "10002" => "Customer already registered",
        "10003" => "Number already registered",
    ];

    public static function make($code){
        $message = self::$errors[$code];
        throw new \Exception($message, $code);
    }

}
