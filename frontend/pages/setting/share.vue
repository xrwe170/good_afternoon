<template>
	<view>
		<u-navbar :title="i18n.share"></u-navbar>
		<text class="d-block text-center font-size-50 m-30 py-30">{{i18n.my_qrcode}}</text>
		<view class="m-30 border-radius-20">
			<view class="d-flex  align-items-center">
				<image :src="user.head_portrait   | retImageUrl" class="border-radius-50per border" mode="aspectFill"
					style="width: 120rpx;height:120rpx;"></image>
				<view class="ml-22">
					<text
						class="d-block font-size-36 font-weight-bold">{{user.account_number || $t("common.plsLogin")}}</text>
					<text class="d-block font-size-22 mt-4" v-if="user.id">ID:{{user.id}}</text>
					<text class="d-block font-size-22 mt-4"
						v-if="user.score">{{i18n.score}}:{{Number(user.score)}}</text>
				</view>
			</view>
			
			<view class="d-flex align-items-center m-20 border-radius-20">
				
				<view class="d-flex align-items-center p-20" style="word-break: break-all; border: 10rpx solid #2e5cd1; border-right: 0rpx solid #2e5cd1; border-top-left-radius: 20rpx; border-bottom-left-radius: 20rpx;">
					<text class="d-block">{{invitation_link}}</text>
				</view>
				
				<view class="d-flex align-items-center p-20" @click="copy()" style="background-color: #2e5cd1; border: 5rpx solid #2e5cd1; color: #ffffff; border-top-right-radius: 20rpx; border-bottom-right-radius: 20rpx;">
					<text>{{ i18n.copyAddress }}</text>
				</view>
			</view>
			<view class="mt-20 mx-auto text-center p-30 border-radius-20">
				<uqrcode ref="uqrcode"></uqrcode>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				user: {},
				invitation_link: '',
			};
		},
		onLoad() {
			this.user = this.$store.state.user
			this.invitation_link = this.$store.state.baseDomain + '/h5/#/pages/common/register?code=' + this.user.extension_code;
		},
		onShow() {
			this.$nextTick(function(){
				this.$refs.uqrcode.make({
						size: 350,
						text: this.$store.state.baseDomain + '/h5/#/pages/common/register?code=' + this.user.extension_code
					})
			})
		},
		computed: {
			i18n() {
				return this.$t("setting")
			},
			
			copy() {
				uni.setClipboardData({
					data:this.invitation_link,
				});
			},
		}
	}
</script>

<style lang="scss">

</style>
