<template>
	<view>
		<u-navbar :title="article.title"></u-navbar>
		<view class="m-30">
			<text class="d-block font-size-32 text-center">{{article.title}}</text>
			<view v-html="article.content" class="mt-20 text-white">
				
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				article:{}
			};
		},
		onLoad(options) {
			const {id} = options
			this.id = id
		},
		onShow() {
			this.getNewsDetail()
		},
		methods:{
			getNewsDetail(){
				this.$u.api.index.getAboutUs().then(res=>{
					this.article = res.message
				})
			}
		}
	}
</script>

<style lang="scss">

</style>
