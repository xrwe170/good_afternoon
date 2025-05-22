<template>
	<view class="pb-50 wrapper">
		<u-navbar :title="i18n.borrowing">
		</u-navbar>
		<view class="mt-100"></view>
		<view class="m-30">
			<button class="primary-button mt-200 mb-50" style="background:#4d4d4d;border:1px solid #fff" @click="goNow()">
				<image src="../../static/image/icon/jied.png" style="width:30px;height:30px;vertical-align: middle;margin-right:10px;"></image>
				{{i18n.borrowing_entry}}
			</button>
			<button class="primary-button mt-200 mb-200" style="background:#4d4d4d;border:1px solid #fff" @click="goNow()">
				<image src="../../static/image/icon/huandai.png" style="width:30px;height:30px;vertical-align: middle;margin-right:10px;"></image>
				{{i18n.repayment_entry}}
			</button>
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
				this.$utils.jump('/pages/kefu/index','redirectTo')
			}
		},
		computed: {
			i18n() {
				return this.$t("home")
			}
		}
	}
</script>

<style lang="scss" scoped>
	page{
		background-image: url('@/static/image/icon/market-bg.png');
		background-size: contain;
		background-position:center 30vh;
		background-repeat: no-repeat;
		background-attachment: fixed;
	}
</style>
