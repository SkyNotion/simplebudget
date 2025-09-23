<?php

namespace App\Custom;

class Responses {

    public static function json($message_array, $status_code = 200){
        return response()->json($message_array, $status_code);
    }

    public static function message($message, $status_code = 200){
        return self::json(['message' => $message], $status_code);
    }

    public static function error($errmsg, $status_code){
        return self::json(['error' => $errmsg], $status_code);
    }

    public static function badRequest($errmsg = 'Bad Request'){
        return self::error($errmsg, 400);
    }

    public static function noContent(){
        return response('No content found', 204);
    }

    public static function notFound(){
        return self::error('Not Found', 404);
    }
}