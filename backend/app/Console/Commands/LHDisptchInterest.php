<?php

namespace App\Console\Commands;

use App\AccountLog;
use App\Setting;
use App\Users;
use App\LhDepositOrder;
use App\Utils\RPC;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class LHDisptchInterest extends Command
{
	protected $signature = "lhdispatch_interest";
	protected $description = "锁仓派息";
	protected $lock_daily_return = "";
	public function handle()
	{
		$res = LhDepositOrder::where([
            'status' => 1,
    ])->where('start_at','<',date("Y-m-d"))
      ->where('last_settle_time','<',date("Y-m-d"))
        ->orWhere('last_settle_time',null)
      ->take(500) ->get();
    $this->comment("start");
    foreach($res as $order){
        LhDepositOrder::dispatchInterest($order->id);
    }
		$this->comment("end");
	}
}