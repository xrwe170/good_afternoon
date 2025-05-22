<template>
	<view class="pb-50">
		<u-navbar :title="i18n.fundTransfer" :borderBottom="false">
			<u-icon name="order" slot="right" size="38" @click="$utils.jump('/pages/fund/transfer_history')">
			</u-icon>
		</u-navbar>
		<view class="m-30">
			<u-subsection :list="nav" :current="currentNav" @change="tabsChange" :button-color="$downColor" :afterWidth="120"></u-subsection>
			
			<view class="d-flex align-items-center justify-content-between select-box"  @click="showCurrencyList=true">
				<view class="title">
					{{i18n.selectCurrency}}
				</view>
				<view class="d-flex align-items-center">
					<text class="font-weight-bold font-size-32 mr-10 name">
						{{fromCurrency.name}}
					</text>
					<image src="/static/image/img/gengduo.png" style="width: 50rpx; height: 50rpx;"></image>
				</view>
				
			</view>
			
			<u-popup v-model="showCurrencyList" mode="bottom" length="60%" :title="i18n.selectCurrency">
				<view class="popup-list">
					<view class="popup-list-item" v-for="(item,index) in transferCurrency" :key="item.text" :class="{active : fromCurrency.id == item.id}" @click="clickCurrencyActionSheet(index)"  >
						<text>{{item.text}}</text>
					</view>
				</view>
			</u-popup>
			
			
		<!-- 	<view class="bg-black text-white border-radius-10 p-30 mt-30 d-flex-between-center">
				<text class="opacity-75 font-size-22">{{i18n.selectCurrency}}</text>
				<view class="" @click="showCurrencyList=true">
					<text class="font-weight-bold font-size-32 mr-10">{{fromCurrency.name}}</text>
					<u-icon name="arrow-down" size="32"></u-icon>
				</view> -->
				
				<!-- <u-action-sheet :list="transferCurrency" v-model="showCurrencyList" @click="clickCurrencyActionSheet">
				</u-action-sheet> -->
				
				<!-- <u-popup v-model="showCurrencyList" mode="bottom" length="60%" :title="i18n.selectCurrency">
					<view class="popup-list">
						<view class="popup-list-item" v-for="(item,index) in transferCurrency" :key="item.text" :class="{active : fromCurrency.id == item.id}" @click="clickCurrencyActionSheet(index)"  >
							<text>{{item.text}}</text>
						</view>
					</view>
				</u-popup>
				
			</view> -->
			
			<view class="tr-box">
				<view class="d-flex-between-center mt-20">
					<text class="font-size-32">{{i18n.from}}</text>
					<view class="" @click="showFromList=true">
						<text class="font-size-32 mr-10">{{fromAccount.text}}</text>
						<u-icon name="arrow-down" size="32"></u-icon>
					</view>
				</view>
				<u-action-sheet :list="fromAccounts" v-model="showFromList" @click="clickFromActionSheet" :cancelText="i18ncommon.cancel">
				</u-action-sheet>
				<view>
					<text class="d-block tr-title">{{i18n.transferNumber}}</text>
					<view class="d-flex align-items-center">
						<view class="flex1">
							<input type="number" v-model="from" class="input" @input="fromNumberChange" @blur="fromNumberBlur"
								@focus="fromNumberFocus">
						</view>
						<view class="d-flex align-items-center fromCurrency">
							<text>{{fromCurrency.name}}</text>
							<view @click="from = balance">{{i18n.all}}</view>
						</view>
					</view>
				</view>
				
				<view class="d-flex-between-center mt-20">
					<text class="opacity-75 font-size-22">{{i18n.availableQuota}}</text>
					<text class="font-weight-bold font-size-28">{{Number(balance)}}</text>
				</view>
			</view>
			<view class="text-center my-30">
				<text class="iconfont icon-zhuanhuan1 text-warning font-size-70" @click="exchange" style="color: #2e5cd1;"></text>
			</view>
			<view class="tr-box">
				<view class="d-flex-between-center mt-10">
					<text class="font-size-32">{{i18n.to}}</text>
					<view class="" @click="showToList=true">
						<text class="font-size-32 mr-10">{{toAccount.text}}</text>
						<u-icon name="arrow-down" size="32"></u-icon>
					</view>
				</view>
				<u-action-sheet :list="toAccounts" v-model="showToList" @click="clickToActionSheet" :cancelText="i18ncommon.cancel">
				</u-action-sheet>
			</view>
			
			<button class="sub-btn-bg mt-50" @click="submit">{{this.$t("common.confirm")}}</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				currentNav:1,
				nav: [{
					name: this.$t("fund.transfer")
				}, {
					name: this.$t("fund.fundTransfer")
				}],
				from:0,
				wallet:{},
				transferCurrency:[],
				fromCurrency:{},
				accounts:[],
				fromAccount:{},
				toAccount:{},
				showFromList:false,
				showToList:false,
				showCurrencyList:false,
				balance:0,
				fromAccounts:[],
				toAccounts:[]
			};
		},
		onLoad() {
			this.accounts = this.$store.state.assetsType.map(item=>{
				item.text = item.type
				return item
			})
		},
		onShow() {
			// 个人币种列表
			this.getUserCurrencyList()
			
		},
		methods: {
			getUserCurrencyList(){
				this.$u.api.wallet.getUserCurrencyList().then(res => {
					this.transferCurrency = res.message.map(item=>{
						item.text = item.name
						return item
					})
					this.fromCurrency = this.transferCurrency[0]
					
					this.checkAccount()
					
					// 获取币币划转的可用列表
					this.$u.api.wallet.getTransferList().then(res => {
						this.wallet  = res.message.wallet
					})
					
					
				})
			},
			checkAccount(){
				const {fromCurrency,accounts} = this
				let account = []
				if(fromCurrency.is_legal){
					account.push(accounts[0])
				}
				if(fromCurrency.is_lever){
					account.push(accounts[2])
				}
				if(fromCurrency.is_match){
					account.push(accounts[1])
				}
				if(fromCurrency.is_micro){
					account.push(accounts[3])
				}
				
				this.fromAccounts = account
				this.toAccounts = account
				
				if(account.length){
					this.fromAccount = account[0]
					if(account.length > 1){
						this.toAccount = account[1]
					}else{
						this.toAccount = account[0]
					}
				}
				this.calcBalance()
			},
			calcBalance(){
				const {fromCurrency,fromAccount} = this
				if(fromAccount.name == 'legal'){
					this.balance = fromCurrency.wallet.legal_balance
				}else if(fromAccount.name == 'lever'){
					this.balance = fromCurrency.wallet.lever_balance
				}if(fromAccount.name == 'change'){
					this.balance = fromCurrency.wallet.change_balance
				}if(fromAccount.name == 'micro'){
					this.balance = fromCurrency.wallet.micro_balance
				}
				
			},
			
			clickFromActionSheet(i){
				const {fromAccounts,toAccount} = this
				if(fromAccounts[i].id == toAccount.id){
					this.exchange()
				}else{
					this.fromAccount = fromAccounts[i]
				}
				this.calcBalance()
			},
			clickToActionSheet(i){
				const {fromAccount,toAccounts} = this
				if(toAccounts[i].id == fromAccount.id){
					this.exchange()
				}else{
					this.toAccount = toAccounts[i]
				}
			},
			exchange(){
				const change = this.fromAccount
				this.fromAccount = this.toAccount
				this.toAccount = change
			},
			tabsChange(index) {
				if(index != this.currentNav){
					uni.redirectTo({
						url:'/pages/fund/transfer'
					})
				}
			},
			fromNumberChange(e) {
				const {
					balance
				} = this
				const value = Number(e.detail.value)
				if (value > Number(balance)) {
					this.from = Number(balance)
					return this.$utils.showToast(this.$t("fund.insufficientBalance"))
				}
			},
			fromNumberFocus(e) {
				const value = Number(e.detail.value)
				if(!value){
					this.from = null
				}
			},
			fromNumberBlur(e){
				const value = Number(e.detail.value)
				if(!value){
					this.from = 0
				}
			},
			clickCurrencyActionSheet(index){
				this.fromCurrency = this.transferCurrency[index]
				this.balance = this.wallet.find(item => item.currency == this.fromCurrency.id)
				this.checkAccount()
				setTimeout(()=>{
					this.showCurrencyList = false
				},500)
			},
			submit(){
				this.$u.throttle(async ()=>{
					const {from,fromCurrency,fromAccount,toAccount,balance} = this
					if(!from || !this.$u.test.amount(from)){
						return this.$utils.showToast(this.$t("fund.plsIptCrtNumber"))
					}
					if (from > Number(balance)) {
						return this.$utils.showToast(this.$t("fund.insufficientBalance"))
					}
					
					const ret = await this.$utils.showModal(this.$t("common.hint"),this.$t("fund.c_transfer"))
					if(!ret) return
					
					this.$u.api.wallet.fundTransfer(fromCurrency.id,from,fromAccount.name,toAccount.name).then(res=>{
						this.from = 0
						this.getUserCurrencyList()
						this.$utils.showToast(this.$t("common.success"))
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
			}
		},
		watch: {
			
		},
		onHide() {
			
		}
	}
</script>

<style lang="scss" scoped>
	
	.select-box {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    padding: 34rpx;
	    margin: 34rpx 0;
	    font-size: 25rpx;
	    font-family: Roboto;
	    font-weight: 700;
	    color: #333;
	}
	
	.tr-box {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius:18rpx;
	    margin-top: 34rpx;
	    padding: 18rpx 34rpx 34rpx 34rpx;
		.tr-title{
			font-size: 25rpx;
			font-family: Roboto;
			font-weight: 700;
			color: #333;
			padding: 38rpx 0;
		}
		
		.input {
		    background-color: #fff;
		    font-size: 34rpx;
		    font-family: Roboto;
		    font-weight: 700;
		}
		.flex1 {
		    flex: 1;
		}
		.fromCurrency {
		    font-size: 25rpx;
		    font-family: Roboto;
		    font-weight: 400;
			&> uni-text {
			    margin-right: 40rpx;
			    color: #333;
			}
			&> uni-view {
				color: #2e5cd1;
			}
		}
	}
	.sub-btn-bg {
	    background: #2e5cd1;
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    color: #fff;
	}
	
	.transfer-input-group {

		.input {
			@extend .font-size-60;
			font-weight: bold;
			height: 100rpx;
			line-height: 100rpx;
			background-color: $uni-color-333;
			padding: 0 30rpx;
		}
		
		.fixed-currency {
			@extend .font-size-32;
			font-weight: bold;
			position: absolute;
			right: 30rpx;
			bottom: 30rpx;
		}
	}
	
	.popup-list{
		padding-bottom: 50rpx;
		.popup-list-item{
			height: 96rpx;
			line-height: 96rpx;
			padding: 0 30rpx;
			font-size: 32rpx;
			position: relative;
			display: flex;
			align-items: center;
			&:before{
				content: "";
				position: absolute;
				left: 30rpx;
				right: 30rpx;
				bottom: 0;
				height: 2rpx;
				background-color: #efefef;
			}
			&.active{
				background-color: #f2f6ff;
				&:after{
					content: "";
					width: 36rpx;
					height: 20rpx;
					background-image: url('../../static/image/icon/setting-icon-20.png');
					background-size: cover;
					position: absolute;
					right: 80rpx;
					top: 50%;
					margin-top: -10rpx;
				}
			}
		}
	}
</style>
