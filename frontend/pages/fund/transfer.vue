<template>
<view class="pb-50">
	<u-navbar :title="i18n.transfer" :borderBottom="false">
		<u-icon name="order" slot="right" size="38" @click="$utils.jump('/pages/fund/transfer_history')">
		</u-icon>
	</u-navbar>
	<view class="m-30">
		<u-subsection :list="nav" :current="currentNav" @change="tabsChange" :button-color="$upColor" :afterWidth="120" ></u-subsection>
		<view class="tr-box">
			<view class="d-flex-between-center pb-30">
				<text class="font-size-32">{{i18n.from}}</text>
				<view class="" @click="showFromList=true">
					<text class="font-weight-bold font-size-32 mr-10">{{fromCurrency.name}}</text>
					<u-icon name="arrow-down" size="32"></u-icon>
				</view>
			</view>
			<text class="d-block tr-title">
				{{i18n.transferNumber}}
			</text>
			<view class="d-flex align-items-center">
				<view class="flex1">
					<input type="number" v-model="from" class="input" @input="fromNumberChange" @blur="fromNumberBlur"
					@focus="fromNumberFocus">
				</view>
				<view class="d-flex align-items-center fromCurrency">
					<text>{{fromCurrency.name}}</text>
					<view @click="fromAllNumberChange">{{i18n.all}}</view>
				</view>
			</view>
			
			<view class="d-flex-between-center mt-20 pb-30">
				<text class="opacity-75 font-size-24">{{i18n.needHandlingFee}}</text>
				<text class="font-weight-bold font-size-28">{{handlingFee}} {{fromCurrency.name}}</text>
			</view>
			<view class="d-flex-between-center pb-30">
				<text class="opacity-75 font-size-24">{{i18n.availableQuota}}</text>
				<text class="font-weight-bold font-size-30">{{balance.change_balance}} {{fromCurrency.name}}</text>
			</view>
			<view class="d-flex-between-center pb-30">
				<text class="opacity-75 font-size-24">{{i18n.exchangeRate}}</text>
				<text class="font-weight-bold font-size-30">{{fee.rate}}</text>
			</view>
			<view class="d-flex-between-center pb-30">
				<text class="opacity-75 font-size-24">{{i18n.handlingFee}}</text>
				<text class="font-weight-bold font-size-30">{{fee.fee}}</text>
			</view>
			
		</view>
		
		<u-action-sheet :list="transferCurrency" v-model="showFromList" @click="clickFromActionSheet" :cancelText="i18ncommon.cancel">
		</u-action-sheet>
			
			
			<!-- <view class="mt-30 transfer-input-group">
				<text class="d-block opacity-75 font-size-28">{{i18n.transferNumber}}</text>
				<view class="position-relative mt-20">
					<input type="number" v-model="from" class="input" @input="fromNumberChange" @blur="fromNumberBlur"
						@focus="fromNumberFocus">
					<text class="fixed-currency">{{fromCurrency.name}}</text>
				</view>
			</view>
			 -->
			
		
		<view class="text-center my-30">
			<text class="iconfont icon-zhuanhuan1 font-size-70" @click="exchange" style="color: #2e5cd1;"></text>
		</view>
		
		<view class="tr-box">
			<view class="d-flex-between-center pb-30">
				<text class="font-size-32">{{i18n.to}}</text>
				<view class="" @click="showToList=true">
					<text class="font-weight-bold font-size-32 mr-10">{{toCurrency.name}}</text>
					<u-icon name="arrow-down" size="32"></u-icon>
				</view>
			</view>
			<text class="d-block tr-title">
				{{i18n.youGet}}
			</text>
			<view class="d-flex align-items-center">
				<view class="flex1">
					<input type="text" v-model="to" class="input" disabled>
				</view>
				<view class="d-flex align-items-center fromCurrency">
					<text>{{toCurrency.name}}</text>
				</view>
			</view>
		</view>
			
			
		<u-action-sheet :list="transferCurrency" v-model="showToList" @click="clickToActionSheet" :cancelText="i18ncommon.cancel">
		</u-action-sheet>
	
		<button class="sub mt-50" @click="submit">{{this.$t("common.confirm")}}</button>
	</view>
</view>
</template>

<script>
	export default {
		data() {
			return {
				currentNav:0,
				nav: [{
					name: this.$t("fund.transfer")
				}, {
					name: this.$t("fund.fundTransfer")
				}],
				wallet: [],
				transferCurrency: [],
				from: 0,
				to: 0,
				fromCurrency: {
					id: 0
				},
				toCurrency: {
					id: 0
				},
				fee: {},
				balance: {},
				showFromList: false,
				showToList: false,
				handlingFee: 0,
				getInterval:null
			};
		},
		onShow() {
			// 获取币币划转的可用列表
			this.getTransferList()
			
			this.getInterval = setInterval(()=>{
				this.getTransferFee()
			},3000)
		},
		methods: {
			getTransferList() {
				this.$u.api.wallet.getTransferList().then(res => {
					this.transferCurrency = res.message.transfer.map(item => {
						item.text = item.name
						return item
					})
					this.wallet = res.message.wallet

					this.fromCurrency = this.transferCurrency[0]
					this.balance = this.wallet.find(item => item.currency == this.fromCurrency.id)
					this.toCurrency = this.transferCurrency[1]
				})
			},
			getTransferFee() {
				const {
					fromCurrency,
					toCurrency
				} = this
				if(!fromCurrency.id || !toCurrency.id) return
				this.$u.api.wallet.getTransferFee(fromCurrency.id, toCurrency.id).then(res => {
					let fee = res.message
					fee.rate = parseFloat((Number(fee.form_price) / Number(fee.to_price)).toFixed(10))
					this.fee = fee
				})
			},
			//点击了from的操作菜单
			clickFromActionSheet(index) {
				this.fromCurrency = this.transferCurrency[index]
			},
			//点击了from的操作菜单
			clickToActionSheet(index) {
				this.toCurrency = this.transferCurrency[index]
			},
			//交换from和to
			exchange() {
				const change = this.fromCurrency
				this.fromCurrency = this.toCurrency
				this.toCurrency = change
			},
			fromAllNumberChange(){
				this.from = this.balance.change_balance;
				const {
					balance,
					fee
				} = this
				const value = Number(this.from)
				if (value > Number(balance.change_balance)) {
					this.from = Number(balance.change_balance)
					return this.$utils.showToast(this.$t("fund.insufficientBalance"))
				}
				this.to = Number((value * fee.rate).toFixed(10))
				this.handlingFee = Number((value * fee.fee).toFixed(10))
			},
			fromNumberChange(e) {
				const {
					balance,
					fee
				} = this
				const value = Number(e.detail.value)
				if (value > Number(balance.change_balance)) {
					this.from = Number(balance.change_balance)
					return this.$utils.showToast(this.$t("fund.insufficientBalance"))
				}
				this.to = Number((value * fee.rate).toFixed(10))
				this.handlingFee = Number((value * fee.fee).toFixed(10))
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
			submit(){
				this.$u.throttle(async ()=>{
					const {from,fromCurrency,toCurrency,balance} = this
					if(!from || !this.$u.test.amount(from)){
						return this.$utils.showToast(this.$t("fund.plsIptCrtNumber"))
					}
					if (from > Number(balance.change_balance)) {
						return this.$utils.showToast(this.$t("fund.insufficientBalance"))
					}
					
					const ret = await this.$utils.showModal(this.$t("common.hint"),this.$t("fund.c_transfer"))
					if(!ret) return
					
					this.$u.api.wallet.transfer(from,fromCurrency.id,toCurrency.id).then(res=>{
						this.from = 0
						this.to = 0
						this.getTransferList()
						this.$utils.showToast(this.$t("common.success"))
					})
					
				},1000)
			},
			tabsChange(index) {
				if(index != this.currentNav){
					uni.redirectTo({
						url:'/pages/fund/fundTransfer'
					})
				}
			}
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
			'fromCurrency.id'(id) {
				this.getTransferFee()
				const {
					wallet
				} = this
				this.balance = wallet.find(item => item.currency == id)
			},
			'toCurrency.id'() {
				this.getTransferFee()
			},
		},
		
		onHide() {
			clearInterval(this.getInterval)
			this.getInterval = null
		},
	}
</script>
<style lang="scss" scoped>
	.tr-box {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    margin-top: 34rpx;
	    padding: 18rpx 34rpx 34rpx 34rpx;
		.tr-title {
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
	
	.sub {
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
</style>
