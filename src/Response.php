<?php
namespace RA;

use Illuminate\Http\Response as LaravelResponse;

class Response
{
    public static function success($data = []) {
        return response()->json(['success' => true, 'data' => $data], LaravelResponse::HTTP_OK);
    }

    public static function error($errors = [], $http_code = LaravelResponse::HTTP_BAD_REQUEST) {
        if ( $errors instanceof \Illuminate\Support\MessageBag ) {
            $messages = [];
            foreach ( $errors->messages() as $message ) {
                $messages[] = $message[0];
            }

            $errors = $messages;
        }
        else if ( !is_array($errors) ) {
            $errors = [$errors];
        }

        if ( env('SET_CORS_HEADER') ) {
            header('Access-Control-Allow-Origin: *');
        }

        $errors = array_unique($errors);

        return response()->json(['success' => false, 'errors' => $errors], $http_code);
    }
}
