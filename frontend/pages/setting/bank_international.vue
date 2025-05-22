<template>
	<view class="">
		<u-navbar :title="i18n.addBank"></u-navbar>
		<view class="mx-30 mt-30 box-shadow  bg-black p-30 border-radius-20 text-white" >
			
			<view class="login-input-group mt-0" v-for="(item, key) in forms">
				<text class="label">{{item}}</text>
				<input type="text" v-model="data[key]" class="login-input">
			</view>

		</view>

		
		<view class="m-30">
			<button class="warning-button py-0" @click="submit" >{{$t("common.confirm")}}</button>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				forms: [],
				data: []
			};
		},
		onShow() {
			this.getCard()
		},
		methods: {
			getCard(){
				this.$u.api.setting.getCardInternational().then(({message})=>{
					this.forms = message.forms
					this.data = message.data
				})
			},
			submit() {
				let {
					i18n
				} = this
				
				let post_data = {}
				for (const i in this.data) {
					if (!this.data[i]) {
						this.$utils.showToast(i18n.allNeed)
						return;
					}
					post_data['data['+i+']'] = this.data[i]
				}
				
				this.$u.api.setting.saveCardInternational(post_data).then(res=>{
					this.$utils.showToast(res.message)
				})
			},
			
		},
		computed: {
			i18n() {
				return this.$t("setting")
			},
		}
	}
</script>

<style lang="scss" scoped>
	
</style>
