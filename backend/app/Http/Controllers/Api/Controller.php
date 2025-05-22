<?php

namespace App\Http\Controllers\Api;

use App\Users;
use App\Token;
use Closure;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;


class Controller extends BaseController
{
    public $user_id;

      public function __construct()
    {
        // if ($_init) {
        //     $token = Token::getToken();
        //     $this->user_id = Token::getUserIdByToken($token);
        // }
        
        // header('Content-Type:application/json');
        // header('Access-Control-Allow-Origin:*');
        // header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        // header('Access-Control-Allow-Headers:x-requested-with,content-type');
        // header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        
    } 

    /**
     * 返回一个错误响应
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message)
    {
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        if (is_string($message)){
            $message=str_replace('massage.', '', __("massage.$message"));
        }
        return response()->json(['type' => 'error', 'message' => $message]);
    }

    /**
     * 返回一个成功响应
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($message,$type=0)
    {
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        if (is_string($message)&&$type==0){
            $message=str_replace('massage.', '', __("massage.$message"));
        }
        return response()->json(['type' => 'ok', 'message' => $message]);
    }

    /**
     * 返回一个成功响应
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function success_ceshi($message)
    {
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        if (is_string($message)){
            $message=str_replace('massage.', '', __("massage.$message"));
        }
        return response()->json(['type' => 'ok', 'message' => $message]);
    }


    public function pageData($paginateObj)
    {
        $results = [
            'data' => $paginateObj->items(),
            'page' => $paginateObj->currentPage(),
            'pages' => $paginateObj->lastPage(),
            'total' => $paginateObj->total()
        ];
        return $this->success($results);
    }

    public function returnStr($str){
        $message=str_replace('massage.', '', __("massage.$str"));
        return $message;
    }
}
