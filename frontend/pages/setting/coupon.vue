<template>
	<view class="text-white">
		<u-navbar :title="$t('setting.coupon')"></u-navbar>
		<view class="mx-30">
			<view class="d-flex flex-wrap my-30 nav">
				<text v-for="(item,index) in nav" :key="item.value" class="item" :class="{active : item.active}" @click="selectNav(index)">{{item.name}}</text>
			</view>
			<view class="list">
				<view class="item" v-for="item in list" :key="item.id" :class="{expired:!item.status}">
					<view class="left">
						<text class="text-1">{{item.price}}</text>
						<text class="text-2">{{item.unit}}</text>
					</view>
					<view class="right">
						<text class="text-1">{{item.name}}</text>
						<text class="text-2">{{$t("setting.expiredTime")}}：{{item.expired_time}}</text>
						<text class="text-3">{{item.project_name}}</text>
					</view>
					<button class="use-immediately">{{$t("setting.useImmediately")}}</button>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				nav:null,
				list:[
					{
						id:1,
						price:1680,
						unit:"USDT",
						name:"注册礼金",
						expired_time:"2021-11-11 01:23",
						project_name:"USDT定期-30天",
						status:1
					},
					{
						id:2,
						price:1680,
						unit:"USDT",
						name:"注册礼金",
						expired_time:"2021-11-11 01:23",
						project_name:"USDT定期-30天",
						status:0
					}
				]
			};
		},
		onShow() {
			this.setNav()
		},
		methods:{
			setNav(){
				const i18n = this.$t("setting")
				const nav = [
					{
						name:this.$t("common.all"),
						value:'all',
						active:true
					},
					{
						name:i18n.lctyq,
						value:'lctyq',
						active:false
					},
					{
						name:i18n.expired,
						value:'expired',
						active:false
					}
				]
				this.nav = nav
			},
			selectNav(index){
				this.nav = this.nav.map((item,ii)=>{
					item.active = ii == index ? true : false
					return item
				})
				
			}
		},
	}
</script>

<style lang="scss" scoped>
.nav{
	.item{
		height: 48rpx;
		line-height: 48rpx;
		font-size: 28rpx;
		padding: 0 40rpx;
		background-color: #fff;
		transition: all .3s ease 0s;
		border-radius: 24rpx;
		margin-right: 10rpx;
		color: #333;
		&.active{
			background-color: #da133a;
			color: #fff;
		}
	}
}

.list{
	.item{
		background-color: $uni-color-black;
		display: flex;
		align-items: center;
		position: relative;
		box-shadow: 0px 0px 8.9px 1.1px rgba(0, 0, 0, 0.1);
		padding: 22rpx 0;
		border-radius: 16rpx;
		margin-bottom: 12rpx;
		text{
			display: block;
		}
		&.expired{
			opacity: .3;
		}
		.left{
			width: 200rpx;
			text-align: center;
			position: relative;
			font-weight: bold;
			padding: 8rpx 0 18rpx 0;
			.text-1{
				font-size: 40rpx;
				color: #da133a;
				letter-spacing: 2rpx;
			}
			.text-2{
				font-size: 22rpx;
				margin-top: 4rpx;
			}
			&:after{
				display: block;
				content: "";
				width: 2rpx;
				position: absolute;
				right: 0;
				top: 0;
				bottom: 0;
				background-color: #eaeaea;
			}
		}
		.right{
			margin-left: 25rpx;
			flex: 1;
			.text-1{
				font-size: 28rpx;
				font-weight: bold;
			}
			.text-2{
				font-size: 20rpx;
				opacity: .5;
				margin-top: 8rpx;
			}
			.text-3{
				font-size: 20rpx;
				color: rgba(51,51,51,.5);
				background-color: #f3f4f6;
				padding: 6rpx 25rpx;
				border-radius: 17.5rpx;
				text-align: center;
				display: inline-block;
				margin-top: 6rpx;
			}
		}
		.use-immediately{
			position: absolute;
			right: 30rpx;
			font-size: 22rpx;
			color: #da133a;
			border:1px solid #da133a;
			border-radius: 16rpx;
			height: 42rpx;
			line-height: 42rpx;
			padding: 0 18rpx;
			background:none;
			&::after{
				display: none;
			}
		}
	}
}
</style>
