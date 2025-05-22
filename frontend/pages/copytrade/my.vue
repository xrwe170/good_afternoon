<template>
	<view class="pb-30">
		<u-navbar title="我的跟单"></u-navbar>
		
		<view class="mx-30 mt-30">
			<view class="deposit">
				<view class="d-flex align-items-center">
					<text class="d-block font-size-28 mr-10">持仓估值</text>
					<text class="iconfont icon-yanjing_xianshi text-white font-size-36"></text>
				</view>
				<view class="mt-30">
					<view class="d-flex align-items-baseline">
						<text class="font-size-40 mr-20">0.00</text>
						<text class="font-size-22">CNY</text>
					</view>
				</view>
				<view class="d-block font-size-22 opacity-50 mt-8"> ≈0.00 USDT </view>
				<view class="earnings-wrap">
					<view class="earnings font-size-22">
						<view class="d-flex justify-content-between opacity-50 font-size-22">
							<text>今日预计收益（USDT）</text>
							<text>收益率</text>
						</view>
						<view class="d-flex justify-content-between font-size-22 mt-16">
							<text>100.0</text>
							<text>5%</text>
						</view>
					</view>
				</view>
				<view class="dealer">
					<text class="mr-20">成为交易员</text>
					<image src="/static/image/icon/copytrade-icon-1.png" style="width: 9rpx;height: 14rpx;"></image>
				</view>
			</view>
			
			<view class="my-nav mt-24">
				<text v-for="item in myCopyTradeNav" class="item" :key="item.value" :class="{active:currentMyCopyTradeNav == item.value}" @click="currentMyCopyTradeNav=item.value">{{item.name}}</text>
			</view> 
			
			<view class="list mt-20">
				<navigator class="item" v-for="item in trader" :key="Math.random()" url="/pages/copytrade/trader">
					<view class="d-flex justify-content-between">
						<view class="w-33">
							<text class="d-block font-size-22 opacity-50">交易员</text>
							<view class="d-flex align-items-center mt-24">
								<image :src="item.user.image" style="width: 50rpx;height: 50rpx;"></image>
								<text class="font-size-28 ml-12">{{item.user.name}}</text>
							</view>	
						</view>
						<view class="w-33 text-center">
							<text class="d-block font-size-22 opacity-50">跟随金额(USDT)</text>
							<text class="d-block mt-24 font-size-28">{{item.copyAmount}}</text>
						</view>
						<view class="w-33 text-right">
							<text class="d-block font-size-22 opacity-50">跟随收益(USDT)</text>
							<text class="d-block mt-24 font-size-28">{{item.copyIncome}}</text>
						</view>
					</view>
					<view class="text-right mt-36">
						<text class="edit">编辑</text>
					</view>
				</navigator>
			</view>
		</view>
		
		
		
		
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				currentMyCopyTradeNav:'current',
				myCopyTradeNav:[{
					name:'当前跟单',
					value:'current'
				},{
					name:'历史跟单',
					value:'history'
				},{
					name:'我的交易员',
					value:'trader'
				}],
				trader:[
					{
						user: {
							image: require('static/image/icon/national-flag-1.gif'),
							name: '小喽喽',
							signature: '忙着赚钱，什么也不想说',
							max_num: 50,
							current_num: 1,
						},
						copyAmount:0,
						copyIncome:0,
					}
				]
			};
		}
	}
</script>

<style lang="scss" scoped>
	.deposit {
		@extend .border-radius-20;
		color: white;
		background-image: linear-gradient(#dc133f, #ff1d4b);
		padding: 0 28rpx;
		padding-top: 38rpx;
		position: relative;

		.earnings-wrap {
			margin: 0 -28rpx;
			margin-top: 30rpx;

			.earnings {
				padding: 30rpx 28rpx 28rpx 28rpx;
				background-color: rgba(255, 255, 255, .1);

			}
		}

		.dealer {
			position: absolute;
			top: 26rpx;
			right: 0;
			color: #7f612e;
			border-radius: 31rpx 0 0 31rpx;
			background-image: linear-gradient(to right, #d9c39b, #fef8cb);
			padding: 14rpx 44rpx;
			display: flex;
			align-items: center;
		}

	}

	.sort-nav {
		margin-top: 16rpx;
		display: flex;
		justify-content: space-between;

		.item {
			font-size: 26rpx;
			color: rgba(51, 51, 51, .5);
			border: 1px solid #eff2fb;
			background-color: #eff2fb;
			border-radius: 8rpx;
			padding: 2rpx 7rpx;
			margin-right: 12rpx;

			&.active {
				background-color: #fff;
				border: 1px solid #d6173a;
				color: #d6173a;
			}
		}
	}

	.list {
		.item {
			@extend .box-shadow,
			.border-radius-20,
			.pt-30,
			.pb-40,
			.px-28, 
			.mb-12;
			.user-num{
				@extend .d-flex,.align-items-center;
				border-radius: 3rpx;
				background-color: #ecf2ff;
				color: #3084fa;
				padding: 7rpx;
			}
			.status{
				font-size: 28rpx;
				padding: 8rpx 20rpx;
				border-radius: 8rpx;
				border: 2rpx solid #fc7600;
			}
			.status-1{
				@extend .status;
				color: #fc7600;
				border-color: #fc7600;
			}
			.status-2{
				@extend .status;
				color: #fff;
				border-color: #3482fe;
				background-color: #3482fe;
			}
			
			.nums-item{
				width: 33.33%;
				margin-top: 20rpx;
				text{
					display: block;
				}
				.text-1{
					font-size: 22rpx;
					opacity: .5;
				}
				.text-2{
					font-size: 32rpx;
					margin-top: 12rpx;
				}
			}
			
			.edit{
				font-size: 28rpx;
				color: #333;
				opacity: .5;
				border: 1px solid #333;
				padding: 5rpx 15rpx;
				border-radius: 8rpx;
			}
		}
	}
	
	.my-nav{
		display: flex;
		align-items: center;
		.item{
			font-size: 32rpx;
			margin-right: 42rpx;
			padding-bottom: 15rpx;
			position: relative;
			&::after{
				position: absolute;
				content: "";
				display: block;
				width: 42rpx;
				height: 6rpx;
				background-color: #fff;
				bottom: 0;
				left: 50%;
				margin-left: -21rpx;
				border-radius: 3rpx;
				transition: all .3s ease 0s;
			}
			&.active::after{
				background-color: #FF0000;
			}
		}
	}

</style>