<template>
	<view class="">
		<u-navbar back-icon-size="34" :title="$t('fund.selectCurrency')" :borderBottom="false"></u-navbar>
		<view class="px-36 py-30">
			<u-search :placeholder="$t('common.search')" input-align="left" :show-action="false" v-model="keyword"></u-search>
		</view>
		<view class="mx-36">
			<view class="d-flex align-items-center justify-content-between item" v-for="item in $store.state.coins" :key="Math.random()" :class="{'cannot':!item.status}" @click="jump(item)">
				<view class="d-flex align-items-center">
					<image class="mr-33" :src="item.image" style="width: 50rpx; height: 50rpx;"></image>
					<text class="name">{{item.name}}{{item.subName && `(${item.subName})`}}</text>
				</view>
				<view class="d-flex align-items-center">
					<text class="type">{{item.type}}</text>
					<image src="/static/image/img/gengduo.png" style="width: 50rpx; height: 50rpx;"></image>
				</view>
			</view>
		</view>
		
	</view>


</template>

<script>
	// 选择币种
	export default {
		data() {
			return {
				url:'',
				keyword:'',
			};
		},
		onLoad(options) {
			this.url = options.url
		},
		methods:{
			jump(item){
				if(!item.status) return
				uni.setStorageSync('selectCoin',item)
				let url = `/pages/fund/${this.url}`
				uni.navigateTo({
					url
				})
			}
		}

	}
</script>

<style lang="scss" scoped>

.mr-33 {
    margin-right: 34rpx;
}

.item{
	box-shadow: 0 10px 32px -4px rgba(24, 39, 75, .1), 0 6px 14px -6px rgba(24, 39, 75, .12);
	border-radius: 15rpx;
	padding: 24rpx 10rpx 24rpx 34rpx;
	margin-top: 34rpx;
	
	.name {
	    font-size: 24rpx;
	    font-family: Roboto;
	    font-weight: 700;
	    color: #333;
	}
	.type{
		font-size: 24rpx;
		font-family: Roboto;
		font-weight: 700;
		color: #1ba27a;
		margin-right: 15rpx;
	}
	&.cannot{
		view{
			opacity: .3;
		}
		.type{
			background-color: #d6d6d6;
		}
	}
}
</style>
