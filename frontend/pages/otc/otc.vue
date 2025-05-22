<template>
	<view>
		<view class="status_bar"></view>
		<!-- 快捷自选区 -->
		<view class="custom-navbar justify-content-between">
			<view class="d-flex align-items-center">
				<u-icon name="arrow-leftward" size="38"></u-icon>
				<view class="d-flex nav">
					<text class="item" :class="{active:activeNav == 0}" @click="activeNav = 0">快捷区</text>
					<text class="item" :class="{active:activeNav == 1}" @click="activeNav = 1">自选区</text>
				</view>
			</view>
			<u-icon name="order" size="38"></u-icon>
		</view>
		<u-gap height="12" bg-color="#f2f2f2"></u-gap>
		<!-- 内容区 -->
		<view class="px-30">
			<!-- 买、卖 -->
			<view class="nav-2 u-border-bottom">
				<text class="item" :class="{active:activeNav2 == 1}" @click="activeNav2 = 1">我要买USDT</text>
				<text class="item ml-56" :class="{active:activeNav2 == 0}" @click="activeNav2 = 0">我要卖USDT</text>
			</view>
			<!-- 快捷区 -->
			<block v-if="activeNav == 0">
				<view class="d-flex-between-center mt-38">
					<view class="text-primary" @click="buyType = !buyType">
						<text class="iconfont icon-qiehuan1 font-size-32"></text>
						<text class="font-size-32 ml-8">{{buyType == '1' ? '按金额购买' : '按数量购买'}}</text>
					</view>
					<view class="d-flex align-items-center px-16" @click="showDefaultCurrency=true"
						style="height: 56rpx;line-height: 56rpx;border-radius: 30rpx;border: 2rpx solid rgba(0,0,0,.3);">
						<image src="/static/image/icon/national-flag-1.gif" style="height: 36rpx;width: 36rpx;"></image>
						<text class="text-primary font-size-28 mx-6">CNY</text>
						<text class="iconfont icon-insert-bottom-full font-size-32 opacity-75 position-relative"
							style="top: -2rpx;"></text>
					</view>
				</view>
				<!-- 输入框 -->
				<view class="d-flex-between-center border-radius-10 mt-26 px-24"
					style="border: 1px solid rgba(51,51,51,.1);">
					<input type="digit" :placeholder="`请输入购买${buyType == '1' ? '金额' : '数量'}`" style="height: 88rpx;">
					<text class="font-weight-bold font-size-28">CNY</text>
				</view>
				<button class=" mt-72" :class="activeNav2 ? 'success-button' : 'error-button'">
					<text class="iconfont icon-lightning font-size-28"></text>
					<text class="ml-8">{{activeNav2 ? '购 买' : '出 售'}}</text>
				</button>
				<u-popup v-model="showDefaultCurrency" mode="bottom" length="60%" :title="$t('setting.selectCoinType')">
					<view class="popup-list">
						<view class="popup-list-item" v-for="item in currencys" :key="item.value"
							:class="{active : item.selected}">
							<image :src="item.image" style="width: 50rpx;height: 50rpx;"></image>
							<text class="pl-18 pr-14 font-size-32">{{item.name}}</text>
							<text class="font-size-32">{{item.symbol}}</text>
						</view>
					</view>
				</u-popup>
			</block>
			<!-- 自选区 -->
			<view v-if="activeNav == 1" class="mt-16">
				<view class="box-shadow border-radius-10 py-18 px-22 mb-12">
					<view class="d-flex-between-center">
						<view class="d-flex align-items-center position-relative">
							<image src="/static/image/icon/avatar.png"
								style="height: 68rpx;width: 68rpx;border-radius: 50%;"></image>
							<view class="dot dot-success"></view>
							<text class="d-block font-size-32 ml-20">安全资金请投入</text>
						</view>
						<text class="font-size-28 opacity-50">5555笔 | 98%</text>
					</view>
					<view class="d-flex-between-center py-6 mt-24">
						<text class="font-size-22 font-weight-bold">需求数量 834756234.13USDT</text>
						<text class="font-size-28 opacity-50">单价</text>
					</view>
					<view class="d-flex-between-center font-weight-bold py-6">
						<text class="font-size-22">单笔限额 ￥10000-￥3000000</text>
						<view class="d-flex align-items-baseline">
							<text class="font-size-22">￥</text>
							<text class="font-size-28">6.078</text>
						</view>
					</view>
					<view class="d-flex-between-center mt-22">
						<image src="/static/image/icon/wechat.png"
							style="height: 58rpx;width: 58rpx;border-radius: 12rpx;"></image>
						<view class="d-flex">
							<button class="white-button font-size-28 px-36"
								style="height: 56rpx;line-height: 56rpx;">聊天</button>
							<button class="error-button font-size-28 px-36 ml-20"
								style="height: 56rpx;line-height: 56rpx;">出售</button>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	import {
		currencys
	} from "./../setting/data.js"
	export default {
		data() {
			return {
				// 1:快捷区,0:自选区
				activeNav: 1,
				// 1:买,0:卖
				activeNav2: 1,
				// 1:按金额购买,0:按数量购买
				buyType: 1,
				//显示币种选择
				showDefaultCurrency: false,
				//比重列表
				currencys,
			};
		}
	}
</script>
<style lang="scss" scoped>
	.nav {
		height: 60rpx;
		line-height: 60rpx;
		border-radius: 30rpx;
		overflow: hidden;
		background-color: #f3f2fa;
		margin-left: 30rpx;
	
	.item {
			height: inherit;
			line-height: inherit;
			padding: 0 34rpx;
			font-size: 32rpx;
			border-radius: inherit;

			&.active {
				background-color: $uni-color-error;
				color: white;
			}
		}
	}

	.nav-2 {
		height: 80rpx;
		line-height: 80rpx;
		padding-top: 7rpx;
		display: flex;

		.item {
			font-size: 32rpx;
			position: relative;
			height: inherit;
			line-height: inherit;

			&::after {
				content: "";
				position: absolute;
				height: 6rpx;
				border-radius: 4rpx;
				background-color: white;
				left: 40%;
				right: 40%;
				bottom: 6rpx;
			}

			&.active::after {
				background-color: #FF0000;
			}
		}
	}

	.popup-list {
		.popup-list-item {
			height: 96rpx;
			line-height: 96rpx;
			padding: 0 30rpx;
			font-size: 32rpx;
			position: relative;
			display: flex;
			align-items: center;

			&:before {
				content: "";
				position: absolute;
				left: 30rpx;
				right: 30rpx;
				bottom: 0;
				height: 2rpx;
				background-color: #efefef;
			}

			&.active {
				background-color: #f2f6ff;

				&:after {
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

	.dot {
		height: 14rpx;
		width: 14rpx;
		border-radius: 50%;
		position: absolute;
		border: 2rpx solid white;
		bottom: 4rpx;
		left: 52rpx;
		background-color: $uni-color-success;

		&.dot-error {
			background-color: $uni-color-error;
		}
	}
</style>