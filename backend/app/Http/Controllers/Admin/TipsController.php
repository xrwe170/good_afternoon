<?php

namespace App\Http\Controllers\Admin;


use App\ChargeReq;
use App\UsersWalletOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipsController extends Controller
{
    public function tips(Request $request)
    {
        $type = $request->get('type');
        if ($type == 1){
            $count = UsersWalletOut::whereHas('user')->where('status',1)->count();

            $code = $count == 0 ? 200 : 100;

            return ['code'=>$code,'type'=>$type];
        }

        if ($type == 2){

            $count = ChargeReq::whereHas('user')->where('status',1)->count();

            $code = $count == 0 ? 200 : 100;

            return ['code'=>$code,'type'=>$type];
        }
        die();
    }

}
