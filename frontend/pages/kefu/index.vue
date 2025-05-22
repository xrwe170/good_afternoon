<template>
	<view>
		<u-navbar :title="i18n.contactUs">
		</u-navbar>
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
		onLoad() {
			this.$u.api.common.getSetting('support_url').then((res) => {
				if (res.type == 'ok') {
					this.url = res.message.value
				}
			});
		},
		methods:{
		},
		computed: {
			i18n() {
				return this.$t("contact")
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
