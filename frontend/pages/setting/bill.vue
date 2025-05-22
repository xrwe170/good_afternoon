<template>
	<view class="text-white">
		<u-navbar :title="$t('setting.bill')">
			<view slot="right" class="nav-right" @click="showFilter=true">
				<text>全部</text>
				<text class="iconfont icon-shaixuan"></text>
			</view>
		</u-navbar>
		<view class="list">
			<view class="item" v-for="item in list" :key="item.id">
				<view class="d-flex-between-center">
					<view class="d-flex align-items-center">
						<image :src="item.image" class="image"></image>
						<text class="font-size-32">{{item.coin}}</text>
						<text class="protocol">{{item.protocol}}</text>
					</view>
					<text class="font-size-32" :style="{color:getColor(item.amount)}">{{item.amount}}</text>
				</view>
				<view class="text-right font-size-22 opacity-50 mt-8 ">
					{{item.currency_unit}}
				</view>
				<view class="d-flex-between-center opacity-50 ml-44 mt-8" >
					<text class="font-size-22">{{item.type}}</text>
					<text class="font-size-22">{{item.date}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				showFilter:false,
				list:[
					{
						id:1,
						image:require('static/image/icon/de.jpg'),
						protocol:"TRC20",
						coin:"USDT",
						amount:"+73452.56",
						currency_unit:"CNY",
						date:"2021-09-04",
						type:"收款"
					},
					{
						id:2,
						image:require('static/image/icon/de.jpg'),
						protocol:"TRC20",
						coin:"USDT",
						amount:"-73452.56",
						currency_unit:"CNY",
						date:"2021-09-04",
						type:"收款"
					}
				]
			};
		},
		methods:{
			getColor(amount){
				amount = +amount
				if(amount >= 0){
					return '#028a62'
				}else{
					return "#b20000"
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
.list{
	margin: 0 36rpx;
	.item{
		padding-top: 30rpx;
		padding-bottom: 12rpx;
		position: relative;
		.image{
			width: 34rpx;
			height: 34rpx;
			border-radius: 50%;
			margin-right: 10rpx;
		}
		.protocol{
			font-size: 20rpx;
			padding:6rpx 24rpx;
			color: #028a62;
			background-color: rgba(33,193,146,.2);
			border-radius: 18rpx;
			margin-left: 20rpx;
		}
		&:after{
			display: block;
			content: "";
			height: 2rpx;
			position: absolute;
			bottom: 0;
			left: 44rpx;
			right: 0;
			background-color: rgba(51,51,51,.05);
		}
	}
}
</style>
