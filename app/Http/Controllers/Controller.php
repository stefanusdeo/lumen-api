<?php

namespace App\Http\Controllers;

use DateTime;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public function respondWithToken($token, $signature)
    {
        return response()->json([
            'token' => $token,
            'signature' => $signature,
            'date' => date_format(date_timestamp_set(new DateTime(), time()), 'c'),
            'token_type' => 'bearer',
            'expires_in' => 3600
        ], 200);
    }

    public function responseInquiryVa($code, $account)
    {
        return response()->json([
            "rc" => $code,
            "message" => "Success",
            "data" => [
                "account_name" => $account
            ]

        ], 200);
    }
}
