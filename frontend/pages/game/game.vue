<template>
	<view>
		<web-view :src="src"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				src:''
			};
		},
		onLoad() {
			this.getGameUrl()
		},
		methods:{
			getGameUrl(){
				this.$u.api.common.getSetting('game_url').then(res=>{
					// console.log(res.message.value);
					this.src = res.message.value
				})
			}
		}
	}
</script>

<style lang="scss">

</style>
