<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Asia/Jakarta');
class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function encode_base64ur($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public function generate_jwt($token, $payload, $secret = 'bbw')
    {
        $headers = array('alg' => 'HS256', 'typ' => 'JWT');
        $headers_encoded = $this->encode_base64ur(json_encode($headers));

        $date = date_format(date_timestamp_set(new DateTime(), time()), 'c');
        $payload_encoded = $this->encode_base64ur(json_encode($payload));

        $stringToSign = $token . ':' . $payload_encoded . ':' . $date;

        $signature = hash_hmac('SHA256', "$headers_encoded.$stringToSign", $secret, true);
        $signature_encoded = $this->encode_base64ur($signature);

        $jwt = "$signature_encoded";
        // dd($payload_encoded);
        return $jwt;
    }

    function is_jwt_valid($request, $signature, $secret = 'bbw')
    {
        // $tokenParts = explode('.', $jwt);

        // $header = base64_decode($tokenParts[0]);
        // $payload = base64_decode($tokenParts[1]);
        $signature_provided = base64_decode($signature);

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
        // $expiration = json_decode($payload)->exp;
        // $is_token_expired = ($expiration - time()) < 0;

        // build a signature based on the header and payload using the secret
        $header = array('alg' => 'HS256', 'typ' => 'JWT');
        $headers_encoded = $this->encode_base64ur(json_encode($header));

        // $date = date_format(date_timestamp_set(new DateTime(), time()), 'c');
        // $payload_encoded = $this->encode_base64ur(json_encode($payload));

        // $stringToSign = $request->bearerToken() . ':' . $payload_encoded . ':' . $request->header('X-BBW-Timestamp');

        // $signature = hash_hmac('SHA256', "$headers_encoded.$stringToSign", $secret, true);
        $base64_url_signature = $this->encode_base64ur($signature);

        dd($signature_provided);
        // verify it matches the signature provided in the jwt
        $is_signature_valid = ($signature === $signature_provided);

        if (!$is_signature_valid) {
            return FALSE;
        } else {
            return TRUE;
        }
    }


    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'username' => 'required|string|unique:users',
            'password' => 'required',
        ]);

        try {
            $user = new User;
            $user->username = $request->input('username');
            $user->password = app('hash')->make($request->input('password'));
            $user->save();

            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'success'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'failed'
            ], 409);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);


        $credentials = $request->only(['username', 'password']);
        // dd($credentials);
        if (!$tokenAuth = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $this->generate_jwt($tokenAuth, $request->only(['username', 'password']));
        return $this->respondWithToken($tokenAuth, $token);
    }

    public function getUserByToken(Request $request)
    {
        $personalAccess = $request->bearerToken();
        $this->is_jwt_valid($request, $request->header('X-BBW-Signature'));
        // dd(Auth::user());
        return response([
            'token' => $personalAccess,
            'user' => Auth::user()->username
        ], 200);
    }

    /**
     * Get user details.
     *
     * @param  Request  $request
     * @return Response
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
}
