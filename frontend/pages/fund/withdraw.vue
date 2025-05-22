<template>
	<view >
		<u-navbar :title="i18n.withdraw2" :borderBottom="false">
			<navigator url="/pages/fund/receive_withdraw_record?type=2" slot="right">
				<u-icon name="order" size="38"></u-icon>
			</navigator>
		</u-navbar>
		<view class="mx-36 py-20">
			
			
			<view class="wi-select wi-shadow d-flex align-items-center justify-content-between"  @click="showSelect=true">
				<view class="select-coin">
					<view class="d-flex align-items-center">
						<text class="font-size-32">{{selectCoin.text + i18n.withdraw2}}</text>
					</view>
				</view>
				<image src="/static/image/img/gengduo.png" style="width: 50rpx; height: 50rpx;"></image>
			</view>
			<u-action-sheet :list="coins" v-model="showSelect" @click="confirmSelect" :cancelText="i18ncommon.cancel"></u-action-sheet>
			
			<!-- <view class="px-30 py-16 box-shadow border-radius-10">
				<view class="select-coin" @click="showSelect=true">
					<view class="d-flex align-items-center">
						<text class="font-size-32">{{selectCoin.text + i18n.withdraw2}}</text>
					</view>
					<view class="">
						<text class="iconfont icon-xiala text-ddd font-size-22 ml-16"></text>
					</view>
				</view>
			</view> -->
			
			<!-- <view class="d-flex font-size-34 mt-28 withdraw_type">
				<text class="mr-20 item" :class="type == 0 ? 'active' : ''" @click="type=0">{{i18n.withdrawToAddress}}</text>
				<text class="mr-20 item" :class="type == 1 ? 'active' : ''" @click="type=1">{{i18n.withdrawToCard}}</text>
				<text class="item" :class="type == 2 ? 'active' : ''" @click="type=2">{{i18n.withdrawToCardInternational}}</text>
			</view> -->
			<!-- 收款地址 -->
			<view class="wi-shadow">
				<view class="d-flex-between-center">
					<text class="d-block withdrawAddress">{{i18n.withdrawAddress}}</text>
				</view>
				<!-- <view class="wi-input-box" @click="showSelectAddress=true">
					<input type="text" v-if="selectAddress" :value="selectAddress.address" :placeholder="i18n.plsSelectWithdrawAddress" disabled>
					<text class="absolute-text iconfont icon-xiala"></text>
				</view> -->
				
				<view class="wi-input-box">
					<!-- <input type="text" v-if="selectAddress" :value="selectAddress.address" :placeholder="i18n.plsSelectWithdrawAddress"> -->
					<input type="text"  v-model="address" :placeholder="i18n.plsSelectWithdrawAddress">
					
				</view>
			</view>
			<!-- 体现到银行卡 -->
			<!-- <view class="u-border-bottom  pb-16 withdraw-input-wrap pb-16 pt-28 " v-if="type == 1">
				<view class="d-flex-between-center">
					<text class="d-block font-size-32">{{i18n.card}}</text>
				</view>
				<view class="input-group">
					<input type="text" v-if="card && card.bank_account" :value="card.bank_account" disabled class="input" >
					<button v-else class="warning-button py-0 font-size-22 w-50 mx-auto" @click="$utils.jump('/pages/setting/bank')">{{i18n.addCard}}</button>
				</view>
			</view> -->
			<!-- 体現到银行卡 國際 -->
			<!-- <template v-if="type == 2">
				<view class="u-border-bottom  pb-16 withdraw-input-wrap pb-16 pt-28 ">
					<view class="d-flex-between-center">
						<text class="d-block font-size-32">{{i18n.card}}</text>
					</view>
					<view class="input-group">
						<input type="text" v-if="card_international && card_international.data[0]" :value="card_international.data[0]" disabled class="input" >
						<button v-else class="warning-button py-0 font-size-22 w-50 mx-auto" @click="$utils.jump('/pages/setting/bank_international')">{{i18n.addCard}}</button>
					</view>
				</view>
			</template> -->
			<!-- 转账数量 -->
			<view class="wi-shadow">
				<view>
					<view class="d-flex-between-center withdrawNumber">
						<text class="d-block font-size-32">{{i18n.withdrawNumber}}</text>
						<text>{{i18n.canUse}} {{change_balance}} {{selectCoin.text}}</text>
					</view>
					<view class="d-flex align-items-center justify-content-between wi-input-box">
						<input type="digit" v-model="number" class="wi-input" :placeholder="i18n.plsIptWithdrawNumber" @blur="checkNumber">
						<text class="absolute-text" @click="number=change_balance">{{i18n.all}}</text>
					</view>
				</view>
			</view>
			<!-- 手续费 -->
			<text class="d-block text-right" style="height: 70rpx;line-height: 70rpx;">
				<text>{{i18n.leastNumber}}</text> <text class="ml-26">{{min_number}} {{selectCoin.text}}</text>
			</text>
			<!-- 到账数量 -->
			<view class="wi-shadow">
				<view>
					<view class="d-flex align-items-center justify-content-between">
						<text class="d-block font-size-32">{{i18n.canGetNumber}}</text>
						<view class="handlingFee">
							<text>{{i18n.handlingFee}}</text>
							<text>{{rate}} {{selectCoin.text}}</text>
						</view>
					</view>
				</view>
				<view class="wi-input-box">
					<input type="text" class="wi-input" disabled :value="canGetNumber">
				</view>
			</view>
			
			
			
			<view class="wi-shadow" v-if="user_info.phone">
				<view>
					<view class="d-flex-between-center withdrawNumber">
						<text class="d-block font-size-32">{{i18ncommon.mobile}}</text>
						<text>{{user_info.phone}}</text>
					</view>
					<view class="d-flex align-items-center justify-content-between wi-input-box">
						<input type="digit" v-model="mobileCode" class="wi-input" :placeholder="i18ncommon.mobilecodePlaceholder">
						
						<view class="send-code-button " :class="mobilehasSend ? 'send' : ''" @click="getMobileCode">
							<text class="absolute-text" v-if="!mobilehasSend">{{i18ncommon.send}}</text>
							<text class="absolute-text" v-else>{{mobileseconds}}s</text>
						</view>
					</view>
				</view>
			</view>
			
			<view class="wi-shadow" v-if="user_info.email">
				<view>
					<view class="d-flex-between-center withdrawNumber">
						<text class="d-block font-size-32">{{i18ncommon.email}}</text>
						<text>{{user_info.email}}</text>
					</view>
					<view class="d-flex align-items-center justify-content-between wi-input-box">
						<input type="digit" v-model="emailCode" class="wi-input" :placeholder="i18ncommon.emailcodePlaceholder">
						
						<view class="send-code-button " :class="emailhasSend ? 'send' : ''" @click="getEmailCode">
							<text class="absolute-text" v-if="!emailhasSend">{{i18ncommon.send}}</text>
							<text class="absolute-text" v-else>{{emailseconds}}s</text>
						</view>
					</view>
				</view>
			</view>
			
			
			
			
			<button class="sub-btn mt-70" @click="withdraw">{{$t("common.confirm")}}</button>
			
			<u-popup v-model="showSelectAddress" mode="bottom">
				<view class="p-30">
					<block v-if="walletAddress.length">
						<view class="item" v-for="item in walletAddress" :key="item.id" @click="selectAddressFunc(item)">
							<view class="d-flex align-items-center">
								<image :src="item.qrcode | retImageUrl" class="border-radius-20 " style="width: 170rpx;height: 170rpx;"></image>
								<view style="flex: 1;" class="mx-20">
									<text class="font-size-28 font-weight-bold d-block">{{i18n.coinType}}: {{item.name}}</text>
									<text class="font-size-28 text-warning font-weight-bold d-block" style="word-wrap:break-word;">{{i18n.address}}: {{item.address}}</text>
									<text class="font-size-28 font-weight-bold d-block">{{i18n.date}}: {{item.update_time}}</text>
								</view>
								<text class="iconfont icon-checkbox-ok text-warning" v-if="selectAddress.id == item.id"></text>
								<text v-else class="iconfont icon-checkbox" ></text>
							</view>
						</view>
					</block>
					<default-page v-else>
						<button class="w-50 mx-auto warning-button py-0 border-radius-20 text-white" @click="$utils.jump('/pages/setting/addWallet')">添加提币地址</button>
					</default-page>
				</view>
			</u-popup>
		</view>
	</view>
</template>
<script>
	// 收款
	export default {
		data() {
			return {
				mobilehasSend: false,
				mobileseconds: 120,
				mobileInterval:null,
				emailhasSend: false,
				emailseconds: 120,
				emailInterval:null,
				mobileCode: null,
				emailCode: null,
				coins: [{
					id:3,
					text:'USDT'
				},{
					id:1,
					text:'BTC'
				},{
					id:2,
					text:'ETH'
				}],
				selectCoin:{},
				showSelect:false,
				min_number:0, //最低提现金额
				change_balance:0,//余额
				rate:0,//手续费,
				number:null,
				showSelectAddress:false,
				originWalletAddress:[],
				walletAddress:[],
				selectAddress:{},
				address:'',
				type:0, //0地址，1银行卡,
				card:{},
				card_international: {},
				user_info:{}
			};
		},
		onLoad() {
			this.selectCoin = this.coins[0]
		},
		onShow(){
			this.getWalletAddressList()
			this.getCard()
			this.getCardInternational()
			this.getInfo()
		},
		methods: {
			async getInfo() {
				uni.showLoading()
			
				const retUser = await this.$u.api.setting.getUserInfo()
				const retUserInfo = retUser.message;
				if(retUserInfo.email){
				  const email = retUserInfo.email.split("@");
				  const emailprefix = email[0].substring(0, 4);
				  retUserInfo.email = emailprefix + "******" + "@" + email[1];
				}
				if(retUserInfo.phone){
				  const phoneprefix = retUserInfo.phone.substring(0, 3);
				  const phonesuffix = retUserInfo.phone.substring(7);
				  retUserInfo.phone = phoneprefix + "****" + phonesuffix;
				}
				this.user_info = retUserInfo
			},
			
			confirmSelect(index){
				this.selectCoin = this.coins[index]
			},
			getWalletInfo(){
				const {id:currency} = this.selectCoin
				this.$u.api.wallet.getWalletInfo(currency).then(res=>{
					const {message:data} = res
					this.change_balance = Number(data.change_balance)
					this.min_number = Number(data.min_number)
					this.rate = Number(data.rate)
					
				})
			},
			checkNumber(e){
				const {i18n,min_number,selectCoin} = this
				const value = Number(e.detail.value)
				
				if(value < min_number && value != 0){
					// this.number = min_number
					return this.$utils.showToast(i18n.leastNumber + min_number + selectCoin.text)
				}
			},
			getWalletAddressList(){
				this.$u.api.setting.getWalletAddressList(this.page,99999).then(res=>{
					this.originWalletAddress = res.message.data
					this.walletAddress = this.originWalletAddress.filter(item=>item.currency == this.selectCoin.id)
					this.selectAddress = this.walletAddress[0]
				})
			},
			selectAddressFunc(item){
				this.selectAddress = item
				setTimeout(()=>{
					this.showSelectAddress=false
				},300);
			},
			withdraw(){
				
				// wallet_sms_captcha
				// wallet_mail_captcha
				const {i18n,selectAddress,address,number,rate,selectCoin,type,card,mobileCode,emailCode,card_international} = this
				// if(selectAddress && !selectAddress.address && type == 0){
				// 	//没有提币地址
				// 	return this.$utils.showToast(i18n.plsSelectWithdrawAddress)
				// }
				// if(!card.bank_account && type == 1){
				// 	//没有银行卡
				// 	return this.$utils.showToast(i18n.plsAddCard)
				// }
				// if(!card_international.bank_account && type == 2){
				// 	//没有银行卡
				// 	return this.$utils.showToast(i18n.plsAddCard)
				// }
				// const address = selectAddress ? selectAddress.address : ''
				
				if(!number){
					//没有金额
					return this.$utils.showToast(i18n.plsIptWithdrawNumber)
				}
				const currency = selectCoin.id
				this.$u.api.wallet.withdraw({address,currency,number,rate,mobileCode,emailCode,type}).then(res=>{
					this.$utils.showToast(res.message)
					this.number = 0
				})
				
			},
			getCard(){
				this.$u.api.setting.getCard().then(res=>{
					this.card = res.message
				})
			},
			getCardInternational(){
				this.$u.api.setting.getCardInternational().then(res=>{
					this.card_international = res.message
				})
			},
			
			getMobileCode(){
				this.$u.throttle(()=>{
					
					if(this.mobilehasSend) return
					
					this.$u.api.wallet.sendSmsCode().then(res => {
						this.$utils.showToast(res.message)
						//倒计时
						this.mobilehasSend = true
						this.mobileInterval = setInterval(() => {
							this.mobileseconds = this.mobileseconds - 1
							if (this.mobileseconds == 0) {
								clearInterval(this.mobileInterval)
								this.mobilehasSend = false
								this.mobileseconds = 120
							}
						}, 1000)
					})
				})
			},
			getEmailCode(){
				// 设置节流,防止频繁点击
				this.$u.throttle(()=>{
					
					if(this.emailhasSend) return
					
					this.$u.api.wallet.sendMailCode().then(res => {
						this.$utils.showToast(res.message)
						//倒计时
						this.emailhasSend = true
						this.emailInterval = setInterval(() => {
							this.emailseconds = this.emailseconds - 1
							if (this.emailseconds == 0) {
								clearInterval(this.emailInterval)
								this.emailhasSend = false
								this.emailseconds = 120
							}
						}, 1000)
					})
					
				},1000)
			},
		},
		computed: {
			i18n() {
				return this.$t("fund")
			},
			i18ncommon() {
				return this.$t("common")
			},
			canGetNumber(){
				const value = this.number - this.rate
				if(value > 0) return value
				return 0
			},
			regNavList() {
				return [{
						name: this.$t('common').mobile
					},
					{
						name: this.$t('common').email
					},
					// {
					// 	name: this.$t('common').account
					// }
				]
			}
		},
		watch:{
			'selectCoin.id'(){
				this.getWalletInfo()
				this.walletAddress = this.originWalletAddress.filter(item=>item.currency == this.selectCoin.id)
				this.selectAddress = this.walletAddress.length ? this.walletAddress[0] : {}
			},
		},
	}
</script>
<style lang="scss" scoped>
	
	.wi-shadow {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    padding: 34rpx 0px 34rpx 34rpx;
	    margin-bottom: 34rpx;
	}
	.wi-input-box {
	    margin-top: 34rpx;
	}
	.withdrawNumber{
	    padding-right: 34rpx;
	}
	.absolute-text{
		font-size: 25rpx;
		font-family: Roboto;
		font-weight: 400;
		color: #2e5cd1;
		margin-right: 34rpx;
	}
	.handlingFee {
	    font-size: 25rpx;
	    font-family: Roboto;
	    font-weight: 400;
	    color: #999;
	    padding-right: 34rpx;
	}
	.sub-btn {
	    background: #2e5cd1;
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    font-size: 30rpx;
	    font-family: Roboto;
	    font-weight: 500;
	    color: #fff;
	    padding: 34rpx 0;
	}
	
	.select-coin {
		@extend .d-flex,
		.align-items-center,
		.justify-content-between;
		border-bottom: 1px solid #f5f5f5;

		&:last-child {
			border-bottom: none;
		}

		.type {
			background-color: $uni-color-success;
			border-radius: 22rpx;
			color: #fff;
			padding: 6rpx 18rpx;
			font-size: 20rpx;
		}

		&.cannot {
			view {
				opacity: .3;
			}

			.type {
				background-color: #d6d6d6;
			}
		}
	}

	.chain {
		margin-top: 16rpx;

		.item {
			font-size: 28rpx;
			height: 46rpx;
			line-height: 46rpx;
			padding: 0 30rpx;
			border-radius: 24rpx;
			border: 1px solid #333;
			color: #333;
			opacity: .5;
			margin-right: 18rpx;

			&.active {
				opacity: 1;
				color: $uni-color-success;
				border-color: $uni-color-success;
				background-color: #e5f8f2;
			}
		}
	}

	.withdraw-input-wrap {
		.input-group {
			position: relative;

			.input {
				height: 80rpx;
				line-height: 80rpx;
				font-size: 28rpx;
				background: none;
				padding: 0;
			}

			.absolute-btn {
				position: absolute;
				font-size: 22rpx;
				height: 42rpx;
				line-height: 42rpx;
				color: #9fa3ab;
				border-radius: 22rpx;
				background-color: #f2f5fc;
				right: 0;
				top: 50%;
				transform: translateY(-50%);
			}

			.absolute-text {
				position: absolute;
				font-size: 28rpx;
				color: #296acf;
				right: 0;
				top: 50%;
				transform: translateY(-50%);
			}
		}
	}
	
	.withdraw-alert{		
		font-size: 22rpx !important;
		padding: 22rpx  34rpx;
		background-color: #4D4D4D;
		margin-top: 54rpx;
		border-radius: 10rpx;
		position: relative;
		&::after{
			content: "";
			display: block;
			width: 28rpx;
			height: 18rpx;
			background: url("/static/image/icon/withdraw-icon-1.png");
			background-size: cover;
			position: absolute;
			top: -18rpx;
			left: 80rpx;
		}
		text{
			line-height: 32rpx;
			display: block;
			color: #ccc !important;
		}
	}
	
	.withdraw_type{
		.item{
			position: relative;
			padding-bottom: 8rpx;
			&:after{
				display: block;
				position: absolute;
				content: "";
				left: 30%;
				bottom: 0;
				right: 30%;
				height: 4rpx;
				border-radius: 6rpx;
				background-color: $uni-color-333;
			}
			&.active:after{
				background-color: $uni-color-warning;
			}
		}
	}
	
</style>
