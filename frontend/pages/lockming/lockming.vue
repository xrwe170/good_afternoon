<template>
	<view class="pb-50">
		<u-navbar :title="i18n.lockming" :borderBottom="false"></u-navbar>
		<view class="loc-top">
			<view class="d-flex align-items-center justify-content-between">
				<view class="fundsUnderCustody">{{i18n.fundsUnderCustody}}</view>
				<view class="dealer" @click="$utils.jump('/pages/lockming/order')">
					<text class="mr-10">{{i18n.entrustedOrders}}</text>
				</view>
			</view>
			
			<view class="d-flex align-items-baseline totalAmount">
				<text>{{totalAmount}}</text>
				<text class="fu-hao">USDT</text>
			</view>
			
			<view class="loc-tab">
				<view class="d-flex align-items-center justify-content-between">
					<view>
						<text class="d-block">{{i18n.estimatedTodayIncome}}</text>
						<text class="d-block loc-tab-money">{{day_rate}}%</text>
					</view>
					<view>
						<text class="d-block">{{i18n.cumulativeIncome}}</text>
						<text class="d-block loc-tab-money">{{total_interest}}</text>
					</view>
					<view>
						<text class="d-block">{{i18n.ordersInCustody}}</text>
						<text class="d-block loc-tab-money">{{orderCount}}</text>
					</view>
				</view>
			</view>
			
			
			<!-- <view class="d-flex align-items-center">
				<text class="d-block font-size-28 mr-10">{{i18n.fundsUnderCustody}}</text>
			</view>
			<view class="mt-30">
				<view class="d-flex align-items-baseline">
					<text class="font-size-44 mr-20">{{totalAmount}}</text>
					<text class="font-size-24">USDT</text>
				</view>
			</view>
			<view class="earnings-wrap">
				<view class="earnings d-grid-columns-3">
					<view class="">
						<text class="d-block opacity-50">{{i18n.estimatedTodayIncome}}</text>
						<text class="d-block mt-10">{{day_rate}}%</text>
					</view>
					<view class="">
						<text class="d-block opacity-50">{{i18n.cumulativeIncome}}</text>
						<text class="d-block mt-10">{{total_interest}}</text>
					</view>
					<view class="">
						<text class="d-block opacity-50">{{i18n.ordersInCustody}}</text>
						<text class="d-block mt-10">{{orderCount}}</text>
					</view>
				</view>
			</view>
			<view class="dealer" @click="$utils.jump('/pages/lockming/order')">
				<text class="mr-10">{{i18n.entrustedOrders}}</text>
			</view> -->
		</view>
		<view class="loc-ul">
			<view class="loc-li" v-for="item in list">
				<view class="d-flex-between-center loc-li-top">
					<text class="font-size-38 font-weight-bold">{{item.currency_name + i18n.lockedPositionsToEarnCoins}}</text>
					<text class="loc-li-tag" @click="jump(item)">{{i18n.lockming}}</text>
				</view>
				<view class="d-flex align-items-center justify-content-between loc-li-tab-box ">
					<view class="">
						<text class="d-block loc-li-teb">{{i18n.minimumSingleTransaction}}</text>
						<text class="d-block loc-value">{{parseFloat(item.save_min)}}</text>
					</view>
					<view class="">
						<text class="d-block loc-li-teb">{{i18n.dailyYield}}</text>
						<text class="d-block loc-value">
						  <text v-if="item.intro">{{item.intro}}</text>
							<text v-else>{{parseFloat(item.interest_rate)}}%</text>
						</text>
					</view>
					<view class="">
						<text class="d-block loc-li-teb">{{i18n.lockUpPeriod}}</text>
						<text class="d-block loc-value">{{item.day}}({{i18n.day}})</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				order:[],
				totalAmount:0,//正在托管的资金
				orderCount:0,//托管中的订单,
				day_rate:0,//日收益率
				total_interest:0,//累计收益
			};
		},
		onShow() {
			// 获取锁仓挖矿列表
			this.getLockming()
			// 获取锁仓挖矿的订单
			this.getLockmingOrder()
		},
		methods: {
			getLockming(){
				this.$u.api.lockming.getLockming().then(res=>{
					this.list = res.message
				})
			},
			getLockmingOrder(){
				this.$u.api.lockming.getLockmingOrder(1,9999).then(res=>{
					let list = res.message.order_list || []
					this.totalAmount = list.reduce((total,item)=>{
						return total + Number(item.amount)
					},0)
					this.day_rate = list.reduce((total,item)=>{
						return total + Number(item.day_rate)
					},0)
					this.day_rate = Number(this.day_rate.toFixed(4))
					this.total_interest = list.reduce((total,item)=>{
						return total + Number(item.total_interest)
					},0)
					this.orderCount = list.length
				})
			},
			jump(item){
				uni.setStorageSync('lockming',item)
				uni.navigateTo({
					url:'/pages/lockming/buy'
				})
			}
		},
		computed: {
			i18n() {
				return this.$t("lockming")
			}
		}
	}
</script>

<style lang="scss" scoped>
	
.loc-top {
	background: #2e5cd1;
	color: #fff;
	.fundsUnderCustody{
		font-size: 38rpx;
		font-family: Roboto;
		font-weight: 500;
		padding: 36rpx 0 32rpx 34rpx;
		
	}
	.dealer{
		width: 174rpx;
		background-color: #fff;
		padding: 15rpx 0 15rpx 34rpx;
		font-size: 24rpx;
		font-family: Roboto;
		font-weight: 500;
		color: #2e5cd1;
		border-radius: 50rpx 0 0px 50rpx;
	}
	.totalAmount {
	    font-size: 58rpx;
	    font-family: Roboto;
	    font-weight: 400;
	    color: #fff;
		& > uni-text:nth-child(1) {
		    padding: 0 15rpx 0 34rpx;
		}
		.fu-hao {
		    font-size: 24rpx;
		}
	}
	.loc-tab {
	    padding: 34rpx;
	    font-size: 24rpx;
	    font-family: Roboto;
	    font-weight: 500;
	    color: #fff;
		.loc-tab-money {
		    font-size: 42rpx;
		    font-weight: 400;
		    padding: 15rpx 0 0 0;
		}
	}
	
}

.loc-ul {
    padding: 0 34rpx 34rpx 34rpx;
	.loc-li {
		box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
		border-radius: 32rpx;
		margin-top: 34rpx;
		.loc-li-top {
		    padding: 39rpx 0 0 34rpx;
			.font-weight-bold{
				font-weight: 700;
			}
			.loc-li-tag{
				border-radius: 50rpx 0 0 50rpx;
				background: #2e5cd1;
				font-size: 24rpx;
				font-family: Roboto;
				font-weight: 400;
				color: #fff;
				padding: 15rpx 15rpx 15rpx 24rpx;
			}
		}
		.loc-li-tab-box {
			padding: 34rpx;
			.loc-li-teb {
			    font-size: 24rpx;
			    font-family: Roboto;
			    font-weight: 500;
			    color: #9e9e9e;
			}
			.loc-value {
			    font-size: 34rpx;
			    font-family: Roboto;
			    font-weight: 400;
			    color: #2e5cd1;
			    padding: 15rpx 0 0 0;
			}
		}
	}
}
	
// .dealer {
// 		position: absolute;
// 		top: 26rpx;
// 		right: 0;
// 		color: #FBE6CC;
// 		border-radius: 31rpx 0 0 31rpx;
// 		background-image: linear-gradient(to right, #574625, #6B552D);
// 		padding: 14rpx 44rpx;
// 		display: flex;
// 		align-items: center;
// 	}
</style>
