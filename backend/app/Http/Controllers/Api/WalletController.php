<?php

namespace App\Http\Controllers\Api;

use AlibabaCloud\SDK\Dysmsapi\V20180501\Models\SendMessageToGlobeRequest;
use AlibabaCloud\Tea\Utils\Utils;
use App\UserLevelModel;
use Illuminate\Support\Carbon;
use App\Conversion;
use App\FlashAgainst;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Utils\RPC;
use App\Http\Requests;
use App\Currency;
use App\Ltc;
use App\LtcBuy;
use App\TransactionComplete;
use App\NewsCategory;
use App\Address;
use App\AccountLog;
use App\Setting;
use App\Users;
use App\UsersWallet;
use App\UsersWalletOut;
use App\WalletLog;
use App\Levertolegal;
use App\LeverTransaction;
use App\Jobs\UpdateBalance;
use PHPMailer\PHPMailer\PHPMailer;

class WalletController extends Controller
{
    
     public function getRateCurrency(Request $request){
        $id=$request->get('id');
        $price=Currency::where('id',$id)->first()->price??1;
        $rate=Setting::getValueByKey('USDTRate', 6.5);
        $rmb=$price*$rate;
        return $this->success([
            'rmb'=>$rmb
        ]);
    }

    public function getRechargeSetting(){
        $bankaccount=Setting::getValueByKey('recharge_bank_account');
        $bankname=Setting::getValueByKey('recharge_bank_name');
        $openbank=Setting::getValueByKey('recharge_open_bank');
        return $this->success([
            'bank_account'=>$bankaccount,
            'bank_name'=>$bankname,
            'open_bank'=>$openbank,
        ]);
    }
    //我的资产
    public function walletList(Request $request)
    {
        $currency_name = $request->input('currency_name', '');
        $user_id = Users::getUserId();
        if (empty($user_id)) {
            return $this->error('参数错误');
        }
        $legal_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currencyCoin', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
                //$query->where("is_legal", 1)->where('show_legal', 1);
                $query->where("is_legal", 1);
            })->get(['id', 'currency', 'legal_balance', 'lock_legal_balance'])
            ->toArray();
            
            

        $legal_wallet['totle'] = 0;
        $legal_wallet['usdt_totle'] = 0;
        foreach ($legal_wallet['balance'] as $k => $v) {
            if(in_array($v['currency'],[3])){
                $legal_wallet['balance'][$k]['is_charge'] = true;
            }else{
                $legal_wallet['balance'][$k]['is_charge'] = false;
            }
            $num = $v['legal_balance'] + $v['lock_legal_balance'];
            //$legal_wallet['totle'] += $num * $v['cny_price'];
            $legal_wallet['usdt_totle'] += $num * $v['usdt_price'];
        }
        
        $legal_wallet['CNY'] = '';
        $change_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currencyCoin', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
            })->get(['id', 'currency', 'change_balance', 'lock_change_balance'])
            ->toArray();
        $change_wallet['totle'] = 0;
        $change_wallet['usdt_totle'] = 0;
        foreach ($change_wallet['balance'] as $k => $v) {
            if(in_array($v['currency'],[1,2,3])){
                $change_wallet['balance'][$k]['is_charge'] = true;
            }else{
                $change_wallet['balance'][$k]['is_charge'] = false;
            }
            $num = $v['change_balance'] + $v['lock_change_balance'];
           // $change_wallet['totle'] += $num * $v['cny_price'];
            $change_wallet['usdt_totle'] += $num * $v['usdt_price'];
        }
        
        $change_wallet['CNY'] = '';
        $lever_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currencyCoin', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
                $query->where("is_lever", 1);
            })->get(['id', 'currency', 'lever_balance', 'lock_lever_balance'])->toArray();
        $lever_wallet['totle'] = 0;
        $lever_wallet['usdt_totle'] = 0;
        foreach ($lever_wallet['balance'] as $k => $v) {
            if(in_array($v['currency'],[])){
                $lever_wallet['balance'][$k]['is_charge'] = true;
            }else{
                $lever_wallet['balance'][$k]['is_charge'] = false;
            }
            $num = $v['lever_balance'] + $v['lock_lever_balance'];
            $lever_wallet['usdt_totle'] += $num * $v['usdt_price'];
        }
        
        $lever_wallet['CNY'] = '';

        $micro_wallet['CNY'] = '';
        $micro_wallet['totle'] = 0;
        $micro_wallet['usdt_totle'] = 0;
        $micro_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currencyCoin', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
                // $query->where("is_micro", 1);
            })->get(['id', 'currency', 'micro_balance', 'lock_micro_balance'])
            ->toArray();
        foreach ($micro_wallet['balance'] as $k => $v) {
            if(in_array($v['currency'],[1,2,3,6,10,29])){
                $micro_wallet['balance'][$k]['is_charge'] = true;
            }else{
                $micro_wallet['balance'][$k]['is_charge'] = false;
            }
            $num = $v['micro_balance'] + $v['lock_micro_balance'];
           // $micro_wallet['totle'] += $num * $v['cny_price'];
            $micro_wallet['usdt_totle'] += $num * $v['usdt_price'];
        }
        $ExRate = Setting::getValueByKey('USDTRate', 6.5);

        //读取是否开启充提币
        $is_open_CTbi = Setting::where("key", "=", "is_open_CTbi")->first()->value;
        return $this->success([
            'legal_wallet' => $legal_wallet,
            'change_wallet' => $change_wallet,
            'micro_wallet' => $micro_wallet,
            'lever_wallet' => $lever_wallet,
            'ExRate' => $ExRate,
            "is_open_CTbi" => $is_open_CTbi
        ]);
    }


    public function currencyList()
    {
        $user_id = Users::getUserId();
        $currency = Currency::where('is_display', 1)->orderBy('sort', 'asc')->get()->toArray();
        if (empty($currency)) {
            return $this->error("暂时还没有添加币种");
        }
        foreach ($currency as $k => $c) {
            $w = Address::where("user_id", $user_id)->where("currency", $c['id'])->count();
            $currency[$k]['has_address_num'] = $w; 
        }
        return $this->success($currency);
    }

    public function addAddress()
    {
        $user_id = Users::getUserId();
        $id = Input::get("currency_id", '');
        $address = Input::get("address", "");
        $notes = Input::get("notes", "");
        if (empty($user_id) || empty($id) || empty($address)) {
            return $this->error("参数错误");
        }
        $user = Users::find($user_id);
        if (empty($user)) {
            return $this->error("用户未找到");
        }
        $currency = Currency::find($id);
        if (empty($currency)) {
            return $this->error("此币种不存在");
        }
        $has = Address::where("user_id", $user_id)->where("currency", $id)->where('address', $address)->first();
        if ($has) {
            return $this->error("已经有此提币地址");
        }
        try {
            $currency_address = new Address();
            $currency_address->address = $address;
            $currency_address->notes = $notes;
            $currency_address->user_id = $user_id;
            $currency_address->currency = $id;
            $currency_address->save();
            return $this->success("添加提币地址成功");
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function addressDel()
    {
        $user_id = Users::getUserId();
        $address_id = Input::get("address_id", '');

        if (empty($user_id) || empty($address_id)) {
            return $this->error("参数错误");
        }
        $user = Users::find($user_id);
        if (empty($user)) {
            return $this->error("用户未找到");
        }
        $address = Address::find($address_id);

        if (empty($address)) {
            return $this->error("此提币地址不存在");
        }
        if ($address->user_id != $user_id) {
            return $this->error("您没有权限删除此地址");
        }

        try {
            $address->delete();
            return $this->success("删除提币地址成功");
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
	public function chargeReq(){
		$user_id = Users::getUserId();

        $currency_id = xssCode(Input::get("currency", ''));
        $type = xssCode(Input::get("type", ''));
        $account = Input::get("account", '');
        $amount = xssCode(Input::get("amount",0));
        
        
        if(empty($currency_id) || empty($amount)) {
        	return $this->error('参数错误1');
        }
        $currency = Db::table('currency')->where('id',$currency_id)->first();
        if(!$currency) {
        		return $this->error('参数错误2');
        }
        $user = Users::find($user_id);
        
        if (empty($user)){
            return $this->error('用户不存在');
        }
        $userLevel = $user['user_level'] > 0 ? UserLevelModel::find($user['user_level']) : null;
        $give = $userLevel ? round(($amount * $userLevel['give'] / 100),8) : 0;
        $give_rate = $userLevel ? $userLevel['give'] : 0;
        $data = [
            'type'=>$type,
        	'uid' => $user_id,
        	'currency_id' => $currency_id,
        	'amount' => $amount,
        	'give' => $give,
        	'give_rate' => $give_rate,
        	'user_account' => $account,
        	'status' => 1,
        	'created_at' => date('Y-m-d H:i:s')
        	];
         Db::table('charge_req')->insert($data);
         return $this->success('申请成功');
	}

    public function hasLeverTrade($user_id)
    {
        $exist_close_trade = LeverTransaction::where('user_id', $user_id)
            ->whereNotIn('status', [LeverTransaction::CLOSED, LeverTransaction::CANCEL])
            ->count();
        return $exist_close_trade > 0 ? true : false;
    }


    private $fromArr = [
        'legal' => AccountLog::WALLET_LEGAL_OUT,
        'lever' => AccountLog::WALLET_LEVER_OUT,
        'micro' => AccountLog::WALLET_MCIRO_OUT,
        'change' => AccountLog::WALLET_CHANGE_OUT,
    ];
    private $toArr = [
        'legal' => AccountLog::WALLET_LEGAL_IN,
        'lever' => AccountLog::WALLET_LEVER_IN,
        'micro' => AccountLog::WALLET_MCIRO_IN,
        'change' => AccountLog::WALLET_CHANGE_IN,
    ];
    private $mome = [
        'legal' => 'c2c',
        'lever' => '合约',
        'micro' => '期权',
        'change' => '闪兑',
    ];

    public function changeWallet(Request $request)  //BY tian
    {
        $type = [
            'legal' => 1,
            'lever' => 3,
            'micro' => 4,
            'change' => 2,
        ];
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency_id", '');
        $number = Input::get("number", '');

        $user = Users::find($user_id);
        if($user->frozen_funds==1){
            return $this->error('资金已冻结');
        }
        $from_field = $request->get('from_field', ""); 
        $to_field = $request->get('to_field', ""); 
        if (empty($from_field) || empty($number) || empty($to_field) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        if ($number < 0) {
            return $this->error('输入的金额不能为负数');
        }
        $from_account_log_type = $this->fromArr[$from_field];
        $to_account_log_type =  $this->toArr[$to_field];
        $memo = $this->mome[$from_field] . '划转' . $this->mome[$to_field];
        if ($from_field == 'lever') {
            if ($this->hasLeverTrade($user_id)) {
                return $this->error('您有正在进行中的杆杠交易,不能进行此操作');
            }
        }
        try {
            DB::beginTransaction();
            $user_wallet = UsersWallet::where('user_id', $user_id)
                ->lockForUpdate()
                ->where('currency', $currency_id)
                ->first();
            if (!$user_wallet) {
                throw new \Exception('钱包不存在');
            }
            $result = change_wallet_balance($user_wallet, $type[$from_field], -$number, $from_account_log_type, $memo);
            if ($result !== true) {
                throw new \Exception($result);
            }
            $result = change_wallet_balance($user_wallet, $type[$to_field], $number, $to_account_log_type, $memo);
            if ($result !== true) {
                throw new \Exception($result);
            }
            DB::commit();
            return $this->success('划转成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('操作失败:' . $e->getMessage());
        }
    }

    public function hzhistory(Request $request)
    {
        $user_id = Users::getUserId();
        $limit = $request->get('limit', 10);

        $arr = [
            AccountLog::WALLET_LEGAL_OUT,
            AccountLog::WALLET_LEVER_OUT,
            AccountLog::WALLET_MCIRO_OUT,
            AccountLog::WALLET_CHANGE_OUT,
            AccountLog::WALLET_LEGAL_IN,
            AccountLog::WALLET_LEVER_IN,
            AccountLog::WALLET_MCIRO_IN,
            AccountLog::WALLET_CHANGE_IN,
            AccountLog::USER_EXCHANGE_ADD,
            AccountLog::USER_EXCHANGE_DOWN,
        ];
        $result = AccountLog::where('user_id',$user_id)->whereIn('type', $arr)->orderBy('id', 'desc')->paginate($limit);
        return $this->success($result);
        
    }
    public function getCurrencyInfo()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        if (empty($currency_id)) return $this->error('参数错误');
        $currencyInfo = Currency::find($currency_id);
        if (empty($currencyInfo)) return $this->error('币种不存在');
        $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first();
        $data = [
            'rate' => $currencyInfo->rate,
            'min_number' => $currencyInfo->min_number,
            'name' => $currencyInfo->name,
            'legal_balance' => $wallet->legal_balance,
            'change_balance' => $wallet->change_balance,
        ];
        return $this->success($data);
    }

    public function getAddressByCurrency()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        if (empty($user_id) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        $address = Address::where('user_id', $user_id)->where('currency', $currency_id)->get()->toArray();
        if (empty($address)) {
            return $this->error('您还没有添加提币地址');
        }
        return $this->success($address);
    }

    public function postWalletOut()
    {
        $user_id = Users::getUserId();
        $type = Input::get("type", '');
        $currency_id = Input::get("currency", '');
        $number = Input::get("number", '');
        $rate = Input::get("rate", '');
        $address = Input::get("address", '');
        $password = Input::get('pay_password');
        $mobileCode = Input::get('mobileCode', '');
        $emailCode = Input::get('emailCode', '');


        if (empty($currency_id) || empty($number) || ($type == 0 && empty($address))) {
            return $this->error('参数错误');
        }
        switch ($currency_id) {
        //BTC
        case '1':
            if (!(preg_match('/^(1|3)[a-zA-Z\d]{24,33}$/', $address) && preg_match('/^[^0OlI]{25,34}$/', $address))) {
                return $this->error('参数错误');
            }
            break;
        //ETH
        case '2':
            
            if (!(preg_match('/^(0x)?[0-9a-fA-F]{40}$/', $address))) {
                 return $this->error('参数错误');
            }
            break;
        }
        if ($number < 0) {
            return $this->error('输入的金额不能为负数');
        }
        $user = Users::getById(Users::getUserId());
        if($user->frozen_funds == 1){
            return $this->error('资金已冻结');
        }
        $currencyInfo = Currency::find($currency_id);
        if ($number < $currencyInfo->min_number) {
            return $this->error('数量不能少于最小值');
        }

        if($user['phone']){
            if ($mobileCode != session('wallet_sms_captcha')) {
                return $this->error('验证码错误');
            }
        }
        if($user['email']){
            if ($emailCode != session('wallet_mail_captcha')) {
                return $this->error('验证码错误');
            }
        }

        try {
            DB::beginTransaction();
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->lockForUpdate()->first();
        
            if ($number > $wallet->change_balance) {
                DB::rollBack();
                return $this->error('余额不足');
            }
            $walletOut = new UsersWalletOut();
            $walletOut->type=$type;
            $walletOut->user_id = $user_id;
            $walletOut->currency = $currency_id;
            $walletOut->number = $number;
            $walletOut->address = $address;
            $walletOut->rate = $rate;
            $walletOut->real_number = $number  - $rate;
            $walletOut->create_time = time();
            $walletOut->status = 1; 
            $walletOut->save();

            $result = change_wallet_balance($wallet, 2, -$number, AccountLog::WALLETOUT, '申请提币扣除余额');
            if ($result !== true) {
                throw new \Exception($result);
            }

            $result = change_wallet_balance($wallet, 2, $number, AccountLog::WALLETOUT, '申请提币锁定余额', true);
            if ($result !== true) {
                throw new \Exception($result);
            }
            DB::commit();
            return $this->success('提币申请已成功，等待审核');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

    public function getWalletAddressIn()
    {
        $user_id = Users::getUserId();

        $currency_id = Input::get("currency", '');
        if (empty($user_id) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        $currencyInfo = Currency::find($currency_id);
        if(!$currencyInfo){
        	 return $this->error('参数错误');
        }
        $legal = UsersWallet::where("user_id", $user_id)
            ->where("currency", $currency_id) //usdt
            ->first();
        if($currency_id==3){//usdt充值地址
            $address=[
                'erc20'=>$currencyInfo->address_erc ?? '', //erc20
                "trc20"=>$currencyInfo->address_omni ?? '' //trc20
                ];
        }else{
            $address=$currencyInfo->address_erc ?? '';
        }
        return $this->success($address);
    }
 
    public function getWalletDetail()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        $type = Input::get("type", '');
        if (empty($user_id) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        $ExRate = Setting::getValueByKey('USDTRate', 6.5);
        if ($type == 'legal') {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'legal_balance', 'lock_legal_balance','address']);
        } else if ($type == 'change') {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'change_balance', 'lock_change_balance','address']);
            
        } else if ($type == 'lever') {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'lever_balance', 'lock_lever_balance','address']);
        } else if ($type == 'micro') {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'micro_balance', 'lock_micro_balance','address']);
        } else {
            return $this->error('类型错误');
        }
        if (empty($wallet)) return $this->error("钱包未找到");

        $wallet->ExRate = $ExRate;
  
        if(in_array($wallet->currency,[1,2,3])){
            $wallet->is_charge = true;
        }else{
            $wallet->is_charge = false;
        }

        $wallet->coin_trade_fee = Setting::getValueByKey('COIN_TRADE_FEE');
        return $this->success($wallet);
    }

    public function legalLog(Request $request)
    {
   
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        $currency = $request->get('currency', 0);
        $type= $request->get('type',0);
        $user_id = Users::getUserId();
        $list = new AccountLog();
        if (!empty($currency)) {
            $list = $list->where('currency', $currency);
        }
        if (!empty($user_id)) {
            $list = $list->where('user_id', $user_id);
        }
        if (!empty($type)) {
            $list = $list->whereHas('walletLog',function($query) use($type){
               $query->where('balance_type',$type);
            });
       }
        $list = $list->orderBy('id', 'desc')->paginate($limit);

        $is_open_CTbi = Setting::where("key", "=", "is_open_CTbi")->first()->value;

        return $this->success(array(
            "list" => $list->items(), 'count' => $list->total(),
            "limit" => $limit,
            "is_open_CTbi" => $is_open_CTbi
        ));
    }

    public function walletOutLog()
    {
        $id = Input::get("id", '');
        $walletOut = UsersWalletOut::find($id);
        return $this->success($walletOut);
    }



    public function getLtcKMB()
    {
        $address = Input::get('address', '');
        $money = Input::get('money', '');
        $wallet = UsersWallet::whereHas('currencyCoin', function ($query) {
            $query->where('name', 'PB');
        })->where('address', $address)->first();
        if (empty($wallet)) {
            return $this->error('钱包不存在');
        }
        DB::beginTransaction();
        try {

            $data_wallet1 = array(
                'balance_type' => 1,
                'wallet_id' => $wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $wallet->change_balance,
                'change' => $money,
                'after' => $wallet->change_balance + $money,
            );
            $wallet->change_balance = $wallet->change_balance + $money;
            $wallet->save();
            AccountLog::insertLog([
                'user_id' => $wallet->user_id,
                'value' => $money,
                'currency' => $wallet->currency,
                'info' => '转账来自钱包的余额',
                'type' => AccountLog::LTC_IN,
            ], $data_wallet1);
            DB::commit();
            return $this->success('转账成功');
        } catch (\Exception $rex) {
            DB::rollBack();
            return $this->error($rex);
        }
    }
    public function sendLtcKMB()
    {
        $user_id = Users::getUserId();
        $account_number = Input::get('account_number', '');
        $money = Input::get('money', '');

        if (empty($account_number) || empty($money) || $money < 0) {
            return $this->error('参数错误');
        }
        $wallet = UsersWallet::whereHas('currencyCoin', function ($query) {
            $query->where('name', 'PB');
        })->where('user_id', $user_id)->first();
        if ($wallet->change_balance < $money) {
            return $this->error('余额不足');
        }

        DB::beginTransaction();
        try {

            $data_wallet1 = array(
                'balance_type' => 1,
                'wallet_id' => $wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $wallet->change_balance,
                'change' => $money,
                'after' => $wallet->change_balance - $money,
            );
            $wallet->change_balance = $wallet->change_balance - $money;
            $wallet->save();
            AccountLog::insertLog([
                'user_id' => $wallet->user_id,
                'value' => $money,
                'currency' => $wallet->currency,
                'info' => '转账余额至钱包',
                'type' => AccountLog::LTC_SEND,
            ], $data_wallet1);

            $url = "http://walletapi.bcw.work/api/ltcGet?account_number=" . $account_number . "&money=" . $money;
            $data = RPC::apihttp($url);
            $data = @json_decode($data, true);
            //            var_dump($data);die;
            if ($data["type"] != 'ok') {
                DB::rollBack();
                return $this->error($data["message"]);
            }
            DB::commit();
            return $this->success('转账成功');
        } catch (\Exception $rex) {
            DB::rollBack();
            return $this->error($rex->getMessage());
        }
    }
    public function PB()
    {
        $user_id = Users::getUserId();
        $wallet = UsersWallet::whereHas('currencyCoin', function ($query) {
            $query->where('name', 'PB');
        })->where('user_id', $user_id)->first();
        return $this->success($wallet->change_balance);
    }
    public function flashAgainstList(Request $request)
    {
        $user_id = Users::getUserId();
        $left = Currency::where('is_match', 1)->get();
        foreach ($left as $k => $v) {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $v->id)->first();
            if (empty($wallet)) {
                $balance = 0;
            } else {
                $balance = $wallet->change_balance;
            }
            $v->balance = $balance;
            $left[$k] = $v;
        }
        $right = Currency::where('is_micro', 1)->get();
        foreach ($right as $k => $v) {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $v->id)->first();
            if (empty($wallet)) {
                $balance = 0;
            } else {
                $balance = $wallet->change_balance;
            }
            $v->balance = $balance;
            $right[$k] = $v;
        }
        return $this->success(['left' => $left, 'right' => $right]);
    }

    public function flashAgainst(Request $request)
    {
        try {
            $l_currency_id = $request->get('l_currency_id', "");
            $r_currency_id = $request->get('r_currency_id', "");
            $num = $request->get('num', 0);

            $user_id = Users::getUserId();
            if ($num <= 0) return $this->error('数量不能小于等于0');
            $p = $request->get('price', 0);
            if ($p <= 0) return $this->error('价格不能小于等于0');

            if (empty($l_currency_id) || empty($r_currency_id))  return $this->error('参数错误哦');

            $left = Currency::where('id', $l_currency_id)->first();
            $right = Currency::where('id', $r_currency_id)->first();
            if (empty($left) || empty($right))  return $this->error('币种不存在');

            //$absolute_quantity = $p * $num / $right->price;
            $absolute_quantity = bc_div(bc_mul($p, $num), $right->price);
            DB::beginTransaction();

            $l_wallet = UsersWallet::where('currency', $l_currency_id)->where('user_id',$user_id)->lockForUpdate()->first();
            
            if (empty($l_wallet)){

                throw new \Exception('钱包不存在');
            }  

            if ($l_wallet->change_balance < $num){

                throw new \Exception('金额不足');
            } 

            $flash_against = new FlashAgainst();
            $flash_against->user_id = $user_id;
            $flash_against->price = $p;
            $flash_against->market_price = $left->price;
            $flash_against->num = $num;
            $flash_against->status = 0;
            $flash_against->left_currency_id = $l_currency_id;
            $flash_against->right_currency_id = $r_currency_id;
            $flash_against->create_time = time();
            $flash_against->absolute_quantity = $absolute_quantity; //实际数量
            $result = $flash_against->save();
            $result1=change_wallet_balance($l_wallet, 2, -$num, AccountLog::DEBIT_BALANCE_MINUS, '闪兑扣除余额');
            $result2=change_wallet_balance($l_wallet, 2, $num, AccountLog::DEBIT_BALANCE_ADD_LOCK, '闪兑增加锁定余额', true);
            if($result1 !== true){
                throw new \Exception($result1);
            }
            if ($result2 !== true) {
                throw new \Exception($result2);
            }

            DB::commit();
            return $this->success('兑换成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage() . '---' . $e->getLine());
        }
    }

    public function myFlashAgainstList(Request $request)
    {
        $limit = $request->get('limit', 10);
        $user_id = Users::getUserId();
        $list = FlashAgainst::orderBy('id', 'desc')->where('user_id', $user_id)->paginate($limit);
        return $this->success($list);
    }

    public function conversion(Request $request)
    {
        $form_currency_id = $request->get('form_currency', '');
        $to_currency_id = $request->get('to_currency', '');
        $balance_filed = 'legal_balance';
        $num = $request->get('num', '');
        if (empty($form_currency_id) || empty($to_currency_id) || empty($num)) {
            return $this->error('参数错误');
        }
        if($num <= 0){
            return $this->error('兑换数量必须大于0');
        }
        $user_id = Users::getUserId();       
        try {
            DB::beginTransaction();
            $form_wallet = UsersWallet::where('user_id', $user_id)->where('currency', $form_currency_id)->lockForUpdate()->first();
            $to_wallet = UsersWallet::where('user_id', $user_id)->where('currency', $to_currency_id)->lockForUpdate()->first();
            if(empty($form_wallet) || empty($to_wallet)){
                DB::rollBack();
                return $this->error('钱包不存咋');
            }
            if ($form_wallet->$balance_filed < $num) {
                DB::rollBack();
                return $this->error('余额不足');
            }
            if (strtoupper($form_wallet->currency_name) == 'USDT') {
                $fee = Setting::getValueByKey('currency_to_usdt_bmb_fee');
                $proportion = Setting::getValueByKey('currency_to_usdt_bmb');
            } elseif (strtoupper($form_wallet->currency_name) == UsersWallet::CURRENCY_DEFAULT) {
                $fee = Setting::getValueByKey('currency_to_bmb_usdt_fee');
                $proportion = Setting::getValueByKey('currency_to_bmb_usdt');
            }
            $totle_num_fee =bc_mul($num,$fee / 100);
            $totle_num = bc_sub($num,$totle_num_fee);
            $totle_num_sj = $proportion * $totle_num;


            $res1=change_wallet_balance($form_wallet, 1, -$totle_num, AccountLog::WALLET_USDT_MINUS, $form_wallet->currency_name . '兑换，' . $to_wallet->currency_name . ',减少' . $form_wallet->currency_name . $totle_num);

            $res2=change_wallet_balance($form_wallet, 1, -$totle_num_fee, AccountLog::WALLET_USDT_BMB_FEE,  $form_wallet->currency_name . '兑换，' . $to_wallet->currency_name . ',减少' . $form_wallet->currency_name . '手续费' . $totle_num_fee);

            $res3=change_wallet_balance($to_wallet, 1, $totle_num_sj, AccountLog::WALLET_BMB_ADD,     $form_wallet->currency_name . '兑换，' . $to_wallet->currency_name . ',增加' . $to_wallet->currency_name . $totle_num_sj);
            if($res1 !== true ){
                DB::rollBack();
                return $this->error($res1);
            }
            if($res2 !== true ){
                DB::rollBack();
                return $this->error($res2);
            }
            if($res3 !== true){
                DB::rollBack();
                return $this->error($res3);
            }

            $conversion = new Conversion();
            $conversion->user_id = $user_id;
            $conversion->create_time = time();
            $conversion->form_currency_id = $form_currency_id;
            $conversion->to_currency_id = $to_currency_id;
            $conversion->num = $num;
            $conversion->fee = $totle_num_fee;
            $conversion->sj_num = $totle_num_sj;
            $conversion->save();
            DB::commit();
            return $this->success('兑换成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function myConversion(Request $request)
    {
        $user_id = Users::getUserId();
        $limit = $request->get('limit', 10);
        $list = Conversion::orderBy('id', 'desc')->where('user_id', $user_id)->paginate($limit);
        return $this->success($list);
    }

    public function conversionList()
    {
        $currency = Currency::where('name', 'USDT')->orWhere('name', UsersWallet::CURRENCY_DEFAULT)->get();
        return $this->success($currency);
    }
    public function conversionSet()
    {
        $fee = Setting::getValueByKey('currency_to_usdt_bmb_fee');
        $proportion = Setting::getValueByKey('currency_to_usdt_bmb');
        $data['usdt_bmb_fee'] = $fee;
        $data['usdt_bmb_proportion'] = $proportion;
        $fee1 = Setting::getValueByKey('currency_to_bmb_usdt_fee');
        $proportion1 = Setting::getValueByKey('currency_to_bmb_usdt');
        $data['bmb_usdt_fee'] = $fee1;
        $data['bmb_usdt_proportion'] = $proportion1;
        $usdt = Currency::where('name', 'USDT')->first();
        $bmb = Currency::where('name', UsersWallet::CURRENCY_DEFAULT)->first();
        $user_id = Users::getUserId();
        $balance_filed = 'legal_balance';
        $usdt_wallet = UsersWallet::where('currency', $usdt->id)->where('user_id', $user_id)->first();
        $data['user_balance'] = $usdt_wallet->$balance_filed;
        $bmb_wallet = UsersWallet::where('currency', $bmb->id)->where('user_id', $user_id)->first();
        $data['bmb_balance'] = $bmb_wallet->$balance_filed;
        return $this->success($data);
    }

    //持险生币
    public function Insurancemoney()
    {

        $user_id = Users::getUserId();
        $wallet = UsersWallet::where('lock_insurance_balance', '>', 0)->where('user_id', $user_id)->first();
        $data = [];

        $data['insurance_balance'] = $wallet->insurance_balance ?? 0;

        $data['lock_insurance_balance'] = $wallet->lock_insurance_balance ?? 0;
        //累计生币
        $data['sum_balance'] = AccountLog::where('user_id', $user_id)->where('type', AccountLog::INSURANCE_MONEY)->sum('value');
        //可用数量
        $data['usabled_balance'] = 0;

        return $this->success($data);
    }

    //持险生币日志
    public function Insurancemoneylogs()
    {

        $user_id = Users::getUserId();
        $limit = Input::get('limit', 10);

        $result = AccountLog::where('user_id', $user_id)->where('type', AccountLog::INSURANCE_MONEY)->orderBy('id', 'desc')->paginate($limit);

        return $this->success($result);
    }


    /**
     * 发送短信
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSmsCode()
    {

        $user_id = Users::getUserId();
        $user = Users::where("id", $user_id)->first();
        if(!$user)
            return $this->error('用户不存在');

        if(!$user['phone'])
            return $this->error('手机号不能为空');


        $verification_code = $this->createSmsCode(6);
        $username = trim($user['phone']);
        $area_codee = str_replace("+","",$user['area_codee']);
        $obj     = $this->createClient();
        $SendMessageToGlobe  = new SendMessageToGlobeRequest(["to" => $area_codee . $username,
            "message" => 'Your verification code is' . '【' . $verification_code . '】']);

        $result = $obj->sendMessageToGlobe($SendMessageToGlobe);
        $result = Utils::toMap($result);

        if($result['statusCode'] == 200 && $result['body']['ResponseCode'] == 'OK'){
            session(['wallet_sms_captcha' => $verification_code]);
            return $this->success("发送成功");
        }

        file_put_contents('SendCodeError.txt', date('Y-m-d H:i:s').'-Phone：'. Utils::toJSONString($result), FILE_APPEND);
        return $this->error("发送失败");

    }

    private function createClient(){
        $config = new \Darabonba\OpenApi\Models\Config([
            "accessKeyId"       => 'LTAI5tCubDyD9aAZ1QFo2vtt',
            "accessKeySecret"   => 'Nsqeupef9sYfmo38bI9ch39s56FWFL'
        ]);
        $config->endpoint = "dysmsapi.ap-southeast-1.aliyuncs.com";
        return new \AlibabaCloud\SDK\Dysmsapi\V20180501\Dysmsapi($config);
    }


    /**
     * 发送邮箱验证 composer 安装的phpmailer
     */
    public function sendMail(Request $request)
    {
        $user_id = Users::getUserId();
        $user = Users::where("id", $user_id)->first();
        if(!$user)
            return $this->error('用户不存在');

        if(!$user['email'])
            return $this->error('手机号不能为空');


        $email = $user['email'];

        //  从设置中取出值
        $username = Setting::getValueByKey('phpMailer_username', '');
        $host = Setting::getValueByKey('phpMailer_host', '');
        $password = Setting::getValueByKey('phpMailer_password', '');
        $port = Setting::getValueByKey('phpMailer_port', 465);
        $mail_from_name = Setting::getValueByKey('submail_from_name', '');
        //实例化phpMailer

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->CharSet = "utf-8";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = $host;
            $mail->Port = $port;//$port;
            $mail->Username = $username;
            $mail->Password = $password;//去开通的qq或163邮箱中找,这里用的不是邮箱的密码，而是开通之后的一个token
            //$mail->SMTPDebug = 2; //用于debug PHPMailer信息
            $mail->setFrom($username, $mail_from_name);//设置邮件来源  //发件人
            $mail->Subject = "Verification code"; //邮件标题
            $code = $this->createSmsCode(6);
            $mail->MsgHTML('Your verification code is' . '【' . $code . '】');   //邮件内容
            $mail->addAddress($email);  //收件人（用户输入的邮箱）
            //dd($mail);
            $res = $mail->send();
            if ($res) {
                session(['wallet_mail_captcha' => $code]);
                return $this->success('发送成功');
            } else {
                return $this->error('操作错误');
            }
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage() . $exception->getLine());
        }
    }

    /**
     * 生成短信验证码
     * @param int $num 验证码位数
     * @return string
     */
    public function createSmsCode($num = 6)
    {
        //验证码字符数组
        $n_array = range(0, 9);
        //随机生成$num位验证码字符
        $code_array = array_rand($n_array, $num);
        //重新排序验证码字符数组
        shuffle($code_array);
        //生成验证码
        $code = implode('', $code_array);
        return $code;
    }
}
