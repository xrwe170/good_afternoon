<template>
	<view class="pb-30">
		<u-navbar title="跟单">
			<navigator url="/pages/copytrade/my" slot="right">
				<image src="/static/image/icon/receive-nav-icon.png" style="width: 40rpx; height: 40rpx;" alt="">
			</navigator>
		</u-navbar>
		<!-- 内容 -->
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
					<text class="mr-10">成为交易员</text>
					<image src="/static/image/icon/copytrade-icon-1.png" style="width: 9rpx;height: 14rpx;"></image>
				</view>
			</view>
			<view style="margin: 30rpx -28rpx 0 -28rpx;">
				<u-gap height="12" bg-color="#f2f2f2"></u-gap>
			</view>
			<view class="mt-36">
				<view class="d-flex align-items-center justify-content-between">
					<text class="font-size-32">交易员列表</text>
					<view class="d-flex align-items-center">
						<image :src="`/static/image/icon/copytrade-icon-${showOnlyHasLocation ? '3' : '2'}.png`"
							style="width: 22rpx;height: 22rpx;" @click="showOnlyHasLocation = !showOnlyHasLocation">
						</image>
						<text class="font-size-22 ml-10">仅显示有位置</text>
					</view>
				</view>
				<text class="font-size-22 opacity-50 mt-10 d-block">数据每小时更新一次</text>
				<template>
					<view class="sort-nav">
						<text v-for="(item,index) in sortNav.slice(0,4)" :key="item.name" class="item"
							:class="{active:index == activeSotrNav}" @click="activeSotrNav=index">{{item.name}}</text>
						<text class="item" style="padding-left: 16rpx;padding-right: 16rpx;"
							@click="showSelectSortNav=true">...</text>
					</view>
					<u-action-sheet :list="actionSheetSortNav" v-model="showSelectSortNav"></u-action-sheet>
				</template>
				<view class="list mt-20">
					<view class="item" v-for="item in list" :key="Math.random()">
						<view class="d-flex align-items-center justify-content-between">
							<view class="d-flex align-items-center">
								<image :src="item.user.image" style="width: 88rpx;height: 88rpx;"></image>
								<view class="ml-14">
									<view class="d-flex align-items-center">
										<text class="font-size-32 mr-8">{{item.user.name}}</text>
										<view class="user-num">
											<text class="iconfont icon-wode1 font-size-26"></text>
											<text
												class="font-size-22 ml-8">{{item.user.current_num}}/{{item.user.max_num}}</text>
										</view>
									</view>
									<text class="d-block font-size-22 opacity-50 mt-10">{{item.user.signature}}</text>
								</view>
							</view>
							<text v-if="item.status == 1" class="status-1">跟单中</text>
							<text v-else class="status-2">+ 跟单</text>
						</view>
						<view class="d-flex flex-wrap justify-content-between mt-8">
							<view class="nums-item">
								<text class="text-1">近2周收益率</text>
								<text class="text-2">{{item.pLRatio2W}}</text>
							</view>
							<view class="nums-item text-center">
								<text class="text-1">近2周胜率</text>
								<text class="text-2">{{item.winRate2W}}</text>
							</view>
							<view class="nums-item text-right">
								<text class="text-1">近2周收益（USDT）</text>
								<text class="text-2">{{item.totalPL2W}}</text>
							</view>
							<view class="nums-item">
								<text class="text-1">累计收益率</text>
								<text class="text-2">{{item.accumPLRatio}}</text>
							</view>
							<view class="nums-item text-center">
								<text class="text-1">累计交易笔数</text>
								<text class="text-2">{{item.transactions}}</text>
							</view>
							<view class="nums-item text-right">
								<text class="text-1">累计跟随人数</text>
								<text class="text-2">{{item.accumFollowers}}</text>
							</view>
						</view>
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
				sortNav: [{
					name: '综合排名',
				}, {
					name: '近2周收益',
				}, {
					name: '累计收益',
				}, {
					name: '累计交易笔数',
				}, {
					name: '当前跟随人数',
				}, {
					name: '近2周胜率',
				}],
				activeSotrNav: 0,
				showOnlyHasLocation: false,
				showSelectSortNav: false,
				list: [{
					user: {
						image: require('static/image/icon/national-flag-1.gif'),
						name: '小喽喽',
						signature: '忙着赚钱，什么也不想说',
						max_num: 50,
						current_num: 1,
					},
					pLRatio2W: '0.00%',
					winRate2W: '0.00%',
					totalPL2W: 2,
					accumPLRatio: '0.00%',
					transactions: '0.00%',
					accumFollowers: '0.00%',
					status: 1,
				}, {
					user: {
						image: require('static/image/icon/national-flag-1.gif'),
						name: '小喽喽',
						signature: '忙着赚钱，什么也不想说',
						max_num: 50,
						current_num: 1,
					},
					pLRatio2W: '0.00%',
					winRate2W: '0.00%',
					totalPL2W: 2,
					accumPLRatio: '0.00%',
					transactions: '0.00%',
					accumFollowers: '0.00%',
					status: 0,
				}]
			};
		},
		computed: {
			actionSheetSortNav() {
				return this.sortNav.map((item, index) => {
					item.text = item.name;
					item.disabled = false;
					if (index == this.activeSotrNav) item.disabled = true;
					return item
				})
			}
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

			.user-num {
				@extend .d-flex,
				.align-items-center;
				border-radius: 3rpx;
				background-color: #ecf2ff;
				color: #3084fa;
				padding: 7rpx;
			}

			.status {
				font-size: 28rpx;
				padding: 8rpx 20rpx;
				border-radius: 8rpx;
				border: 2rpx solid #fc7600;
			}

			.status-1 {
				@extend .status;
				color: #fc7600;
				border-color: #fc7600;
			}

			.status-2 {
				@extend .status;
				color: #fff;
				border-color: #3482fe;
				background-color: #3482fe;
			}

			.nums-item {
				width: 33.33%;
				margin-top: 20rpx;

				text {
					display: block;
				}

				.text-1 {
					font-size: 22rpx;
					opacity: .5;
				}

				.text-2 {
					font-size: 32rpx;
					margin-top: 12rpx;
				}
			}
		}
	}
</style>
