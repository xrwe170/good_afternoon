<template>
	<view>
		<text class="iconfont icon-fanhui back" @click="back"></text>
		<web-view :src="url"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				url:''
			};
		},
		onLoad(options) {
			this.url = options.url
		},
		methods:{
			back(){
				uni.navigateBack({
					delta:1
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
.back{
	position: fixed;
	left: 20rpx;
	top: 44rpx;
	z-index: 99999;
	color: #fff;
}
</style>
