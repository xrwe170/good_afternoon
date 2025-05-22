<template>
	<view class="pb-50">
		<u-navbar :title="i18n.lockedPositionList" :borderBottom="false"></u-navbar>
		<view class="loc-order-box">
			<view class="loc-order-li" v-for="item in list">
				<view class="d-flex align-items-center justify-content-between loc-order-li-top">
					<view class="d-flex align-items-center">
						<view class="d-block name">
							{{item.currency_name + i18n.lockedPositionsToEarnCoins}}
						</view>
						<u-button type="error" @click="redemption(item.id)" class="ml-20" v-if="item.status == 1" size="mini">{{i18n.redemption}}</u-button>
					</view>
					<view>
						<view class="d-flex align-items-center">
							<view class="loc-tag loc-tag-success" v-if="item.status == 1">
								{{i18n.inProgress}}
							</view>
							<view class="loc-tag loc-tag-error" v-if="item.status == 2">
								{{i18n.redeemed}}
							</view>
						</view>
					</view>
				</view>
				<view class="d-flex justify-content-between list">
					<view>
						<text class="d-block">{{i18n.numberOfCoinsDeposited}}(USDT)</text>
						<text class="d-block mt-10">{{parseFloat(item.amount)}}</text>
					</view>
					<view>
						<text class="d-block">{{i18n.dailyYield}}</text>
						<text class="d-block mt-10">{{parseFloat(item.day_rate)}}%</text>
					</view>
					<view>
						<text class="d-block">{{i18n.lockUpTime}}</text>
						<text class="d-block mt-10">{{item.start_at}}</text>
					</view>
					
					<view class="mt-20">
						<text class="d-block">{{i18n.expiryTime}}</text>
						<text class="d-block mt-10">{{item.end_at}}</text>
						</view>
					<view class="mt-20">
						<text class="d-block">{{i18n.earlyRedemptionPenalty}}</text>
						<text class="d-block mt-10">{{parseFloat(item.cancel_fee.toFixed(2))}} USDT</text>
						</view>
				</view>
				
				
				
				<!-- <view class="d-flex-between-center">
					<view class="d-flex align-items-center">
						<text class="tag tag-success" v-if="item.status == 1">{{i18n.inProgress}}</text>
						<text class="tag tag-error" v-else-if="item.status == 2">{{i18n.redeemed}}</text>
						<text class="d-block font-size-32 ml-12">{{item.currency_name + i18n.lockedPositionsToEarnCoins}}</text>
					</view>
					<u-button type="error" @click="redemption(item.id)" class="mr-0" v-if="item.status == 1" size="mini">{{i18n.redemption}}</u-button>
				</view>
				<view class="d-grid-columns-3 mt-30">
					<view class="">
						<text class="d-block font-size-24 opacity-50">{{i18n.numberOfCoinsDeposited}}(USDT)</text>
						<text class="d-block font-size-28 mt-10 font-weight-bold">{{parseFloat(item.amount)}}</text>
					</view>
					<view class="">
						<text class="d-block font-size-24 opacity-50">{{i18n.dailyYield}}</text>
						<text class="d-block font-size-28 mt-10 font-weight-bold">{{parseFloat(item.day_rate)}}%</text>
					</view>
					<view class="">
						<text class="d-block font-size-24 opacity-50">{{i18n.lockUpTime}}</text>
						<text class="d-block font-size-28 mt-10 font-weight-bold">{{item.start_at}}</text>
					</view>
					<view class="mt-20">
						<text class="d-block font-size-24 opacity-50">{{i18n.expiryTime}}</text>
						<text class="d-block font-size-28 mt-10 font-weight-bold">{{item.end_at}}</text>
					</view>
					<view class="mt-20">
						<text class="d-block font-size-24 opacity-50">{{i18n.earlyRedemptionPenalty}}</text>
						<text class="d-block font-size-28 mt-10 font-weight-bold text-error">{{parseFloat(item.cancel_fee.toFixed(2))}}USDT</text>
					</view> -->
				</view>
			</view>
			<default-page v-if="!list.length"></default-page>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				page:1,
				canGet:true,
				limit:5,
				list:[],
			};
		},
		onShow() {
			this.page = 1
			this.canGet = true
			this.list = []
			this.getLockmingOrder()
		},
		methods:{
			getLockmingOrder(){
				if(!this.canGet) return
				this.$u.api.lockming.getLockmingOrder(this.page,this.limit).then(res=>{
					let list = res.message.order_list || []
					if(list.length){
						this.list = [...this.list,...list]
						this.page++
					}else{
						this.canGet = false
					}
					
				})
			},
			async redemption(id){
				const ret = await this.$utils.showModal(this.$t("common.hint"),this.$t("lockming.c_redemption"))
				if(!ret) return
				this.$u.api.lockming.redemption(id).then(res=>{
					this.$utils.showToast(res.message)
					this.getLockmingOrder()
				})
			}
		},
		computed: {
			i18n() {
				return this.$t("lockming")
			}
		},
		onReachBottom() {
			this.getLockmingOrder()
		}
	}
</script>

<style lang="scss">
.loc-order-box{
    padding: 0 34rpx 34rpx;
	.loc-order-li{
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 32rpx;
	    margin-top: 34rpx;
		.loc-order-li-top {
		    padding: 39rpx 0 32rpx 34rpx;
			.name {
			    font-size: 38rpx;
			    font-family: Roboto;
			    font-weight: 500;
			    color: #333;
			}
			.loc-tag {
			    display: block;
			    padding: 12rpx 15rpx 12rpx 24rpx;
			    font-size: 24rpx;
			    font-family: Roboto;
			    font-weight: 400;
			    color: #fff;
			    border-radius: 50rpx 0 0 50rpx;
				&.loc-tag-success {
					background: red;
				}
				&.loc-tag-error {
					background: #2e5cd1;
				}
			}
		}
		.list {
		    flex-wrap: wrap;
		    padding: 0 0 34rpx 34rpx;
			& > uni-view {
			    width: 33%;
				& > uni-text{
				    font-size: 24rpx;
				    font-family: Roboto;
				    font-weight: 500;
				    color: #9e9e9e;
				}
				& > uni-text:nth-child(2) {
					font-size: 34rpx;
					font-weight: 400;
					color: #2e5cd1;
				}
			}
		}
	}
}
</style>
