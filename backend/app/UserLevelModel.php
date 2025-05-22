<?php

/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserLevelModel extends Model
{
    protected $table = 'user_level';
    public $timestamps = true;


    public static function getLevelName($level)
    {
        if (empty($level)){
            return ['无',null];
        }
        $l = self::find($level);
        return [$l->name,$l->pic];
    }

    public static function checkUpgrade($req)
    {
        $user = Users::find($req->uid);

        $list = DB::table('charge_req')
            ->join('currency', 'currency.id', '=', 'charge_req.currency_id')
            ->where('charge_req.uid',$user->id)
            ->select('charge_req.*','currency.price','currency.rmb_relation')
            ->get();
        $cny = 0;
        foreach ($list as $item) {
            $c = ($item->amount + $item->give) * $item-> price * $item->rmb_relation;
            $cny += $c;
        }
        // 查找级别
        $level = self::where('amount','<=',$cny)
            ->orderBy('amount', 'desc')
            ->first();
        if ($level && $user->user_level < $level->id){ // 升级
            $user->user_level = $level->id;
            $user->save();
            DB::table('user_level_log')->insert([
                "user_id" => $user->id,
                "level_id" => $level->id,
                "type" => 2,
                "create_time" => time(),
            ]);
        }
    }

}
