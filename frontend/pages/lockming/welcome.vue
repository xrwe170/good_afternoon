<template>
	<view class="pb-50">
		<u-navbar :title="i18n.lockming" :borderBottom="false">
		</u-navbar>
		
		<view class="m-30">
			<text class="d-block font-size-40 text-center">{{i18n.l_alert_1}}</text>
			<view class="mt-60 mb-60 text-center font-size-20">
				<text>{{i18n.l_alert_2}}</text>
			</view>
		</view>
		
		<view class="m-30">
			<view class="p-30 box-shadow border-radius-20 mb-20">
				<text class="d-block font-size-40 text-center mb-40">{{i18n.features}}</text>
				<view class="d-grid-columns-2">
					<view class="d-flex flex-direction-column align-items-center">
						<text class="d-block font-size-40 mb-24">{{i18n.onDemand}}</text>
						<text class="d-block font-size-34">{{i18n.dividendPeriod}}</text>
					</view>
					<view class="d-flex flex-direction-column align-items-center">
						<text class="d-block font-size-40 mb-24">{{i18n.issuedDaily}}</text>
						<text class="d-block font-size-34">{{i18n.currentInterest}}</text>
					</view>
				</view>
				<view class="d-grid-columns-3 mt-60">
					<view class="d-flex align-items-center flex-direction-column text-center">
						<image class="d-block" src="../../static/image/icon/icon-safe.png" mode="aspectFit" style="width:31px;height:36px;"></image>
						<text class="font-size-30 mt-12" v-html="i18n.feature_1"></text>
					</view>
					<view class="d-flex align-items-center flex-direction-column text-center">
						<image class="d-block" src="../../static/image/icon/icon-wallet.png" mode="aspectFit" style="width:31px;height:34px;"></image>
						<text class="font-size-30 mt-12" v-html="i18n.feature_2"></text>
					</view>
					<view class="d-flex align-items-center flex-direction-column text-center">
						<image class="d-block" src="../../static/image/icon/icon-zs.png" mode="aspectFit" style="width:36px;height:36px;"></image>
						<text class="font-size-30 mt-12" v-html="i18n.feature_3"></text>
					</view>
				</view>
			</view>
		</view>
		
		<view class="m-30">
			<view class="p-30 box-shadow border-radius-20 mb-20">
				<text class="d-block font-size-40 text-center mb-40">{{i18n.forInstance}}</text>
				<view>
					<text class="font-size-40">{{i18n.incomeCalculation}}</text>
					<view class="mt-10 mb-10 font-size-30" v-html="i18n.forInstanceDesc"></view>
				</view>
			</view>
		</view>
		
		<view class="m-30">
			<view class="p-30 box-shadow border-radius-20 mb-20">
				<text class="d-block font-size-40">{{i18n.aboutLiquidatedDamages}}</text>
				<view class="mt-10 mb-10 font-size-30" v-html="i18n.aboutLiquidatedDamagesContent"></view>
			</view>
		</view>
		
		<view class="m-30">
			<button class="sub-btn-bg mt-28" @click="goNow()">{{i18n.joinNow}}</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			}
		},
		methods: {
			goNow() {
				let item = uni.getStorageSync('lockming') || {}
				if (item.currency_id) {
					this.$utils.jump('/pages/lockming/buy','redirectTo')
				} else {
					this.$utils.jump('/pages/lockming/lockming','redirectTo')
				}
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
	.box-shadow {
	    box-shadow: 0 0 10px 0 rgba(0, 0, 0, .1);
	}
	.sub-btn-bg {
	    background: #2e5cd1;
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    color: #fff;
	}
	// .welcome {
	// 	background-image: url('./../../static/image/icon/home-bg.png');
	// 	background-repeat: no-repeat;
	// 	background-size: 100% 530rpx;
	// 	background-position: left top;
	// }
</style>
