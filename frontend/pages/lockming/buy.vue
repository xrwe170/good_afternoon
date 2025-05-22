<template>
	<view class="pb-50">
		<u-navbar  :title="project.currency_name + i18n.lockedPositionsToEarnCoins" ></u-navbar>
		<view class="buy-top mt-30 mx-30">
			<view class="d-flex align-items-center">
				<text
					class="d-block font-size-28 mr-10">{{project.currency_name + i18n.lockedPositionsToEarnCoins}}</text>
			</view>
			<view class="mt-10">
				<view class="d-flex align-items-baseline">
					<text class="font-size-32 mr-20">{{i18n.get + project.currency_name}}</text>
				</view>
			</view>
			<view class="mt-20 earnings-wrap">
				<view class="earnings d-grid-columns-3">
					<view class="">
						<text class="d-block">{{i18n.minimumSingleTransaction}}</text>
						<text class="d-block color mt-10">{{parseFloat(project.save_min)}}</text>
					</view>
					<view class="">
						<text class="d-block">{{i18n.dailyYield}}</text>
						<text class="d-block color mt-10">{{parseFloat(project.interest_rate)}}%</text>
					</view>
					<view class="">
						<text class="d-block">{{i18n.lockUpPeriod}}</text>
						<text class="d-block color mt-10">{{project.day}}({{i18n.day}})</text>
					</view>
				</view>
			</view>
		</view>
		<view class="m-30">
			<view class="p-30 box-shadow border-radius-20">
				<view class="d-flex-between-center">
					<text class="d-block">{{i18n.dividendTime}}</text>
					<text class="color font-weight-bold">{{i18n.daily}}</text>
				</view>
				<view class="d-flex-between-center mt-20">
					<text class="d-block">{{i18n.escrowFunds}}</text>
					<text class="color font-weight-bold">{{i18n.returnOnExpiration}}</text>
				</view>
				<view class="d-flex-between-center mt-20">
					<text class="d-block">{{i18n.redemptionInAdvance}}</text>
					<text class="color font-weight-bold">2%</text>
				</view>
			</view>
			<view class="p-30 box-shadow border-radius-20 mt-30 ">
				<view class="position-relative">
					<text class="d-block">{{i18n.numberOfCoinsDeposited}}({{balance.currency_name}})</text>
					<input type="number" v-model="amount" class="lockming-input mt-10">
					<text class="tag color position-absolute" @click="setAll" style="right: 20rpx;bottom: 14rpx;">{{i18n.all}}</text>
				</view>
				<view class="d-flex-between-center mt-20">
					<text class="d-block">{{i18n.estimatedIncome}}({{balance.currency_name}})</text>
					<text class="color font-weight-bold">{{estimatedIncome}}</text>
				</view>
				<view class="d-flex-between-center mt-20">
					<text class="d-block">{{i18n.availableAssets}}({{balance.currency_name}})</text>
					<text class="color font-weight-bold">{{balance.change_balance}}</text>
				</view>
			</view>
		</view>
		<view class="footer">
			<button class="warning-button font-size-28" @click="submit">{{i18n.subscribe}}</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				balance: {},
				project: {},
				amount:null
			};
		},
		onShow() {
			const project = uni.getStorageSync('lockming') || {}
			// 清除storage
			uni.removeStorageSync('lockming')
			if (!project.currency_id) {
				this.$utils.showToast(this.$t("common.paramsWrong"))
				setTimeout(() => {
					uni.navigateBack({
						delta: 1
					})
				}, 1000)
				return
			}
			this.project = project

			this.getWalletDetail()
		},
		methods: {
			getWalletDetail() {
				const {
					project,
				} = this
				const currency_id = project.currency_id
				this.$u.api.market.getWalletDetail(currency_id, 'change').then(res => {
					this.balance = res.message
				})
			},
			submit(){
				this.$u.throttle(()=>{
					const {amount,balance,project,i18n} = this
					if(!amount || !this.$u.test.amount(amount)){
						return this.$utils.showToast(i18n.p_number)
					}
					if(amount < Number(project.save_min)){
						return this.$utils.showToast(i18n.leastSingle + Number(project.save_min))
					}
					if(amount > Number(balance.change_balance)){
						return this.$utils.showToast(i18n.insufficientBalance)
					}
					this.$u.api.lockming.lockming(project.id,amount).then(res=>{
						this.$utils.showToast(res.message)
						setTimeout(()=>{
							uni.navigateBack({
								delta:1
							})
						},1200)
					})
				},1200)
				
			},
			setAll(){
				this.amount = this.balance.change_balance
			}
		},
		computed: {
			i18n() {
				return this.$t("lockming")
			},
			estimatedIncome(){
				const {balance,project,i18n} = this
				const amount = this.amount ? Number(this.amount) : 0
				
				return amount * project.day * Number(project.interest_rate) / 100
			}
		}
	}
</script>

<style lang="scss" scoped>
	.buy-top {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 32rpx;
	    padding: 34rpx;
	}
	.color {
	    color: #2e5cd1;
	}
	.box-shadow {
	    box-shadow: 0 0 10px 0 rgba(0, 0, 0, .1);
	}
	
	.lockming-input {
		border: 2rpx solid #666;
		border-radius: 10rpx;
		display: block;
		padding: 10rpx 20rpx;
		font-weight: bold;

		.input-placeholder {
			font-size: 22rpx;
			color: rgba(51, 51, 51, 0.4);
			font-weight: normal;
		}
	}
	
	.footer {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		@extend .box-shadow;
		padding: 30rpx;
		border-radius: 20rpx 20rpx 0 0;
	}
</style>
