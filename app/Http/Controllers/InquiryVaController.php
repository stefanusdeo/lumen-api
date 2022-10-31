<?php

namespace App\Http\Controllers;

use App\Models\InquiryVa;
use App\Models\PersonalAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InquiryVaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|numeric',
            'reference_number' => 'required|numeric',
            'virtual_account' => 'required|numeric'
        ]);

        if(!$request->header('X-BBW-Timestamp') || !$request->header('X-BBW-Signature')){
            return response([
                'rc'=> '001',
                'message'=>'header Not Complate'
            ],401);
        }

        try {
            DB::beginTransaction();

            $clientArray = str_split($request->client_id);
            $temp = [];
            $tempBach = false;
            foreach ($clientArray as $key) {
                if (!$key == 0 && $tempBach == false) {
                    $tempBach = true;
                }

                if ($tempBach) {
                    array_push($temp, $key);
                }
            }

            $payload = [
                'client_id' => implode($temp),
                'reference_number' => str_pad($request->reference_number, 12, '0', STR_PAD_LEFT),
                'virtual_account' => $request->firtual_account
            ];
            InquiryVa::created($payload);
            $user = User::whereId(implode($temp))->first();

            DB::commit();
            return  $this->responseInquiryVa("000", $user->username);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                'success' => false
            ], 401);
        }
    }
}
