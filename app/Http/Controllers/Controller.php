<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
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
