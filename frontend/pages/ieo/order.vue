<template>
	<view>
		<u-navbar :title="i18n.mySubscribe"></u-navbar>
		<view class="text-white">
			<view class="d-grid py-20 font-size-22 opacity-50" style="grid-template-columns:1.4fr .8fr 1fr 1fr 1.4fr;">
				<text class="d-block text-center">{{i18n.subscriptionTime}}</text>
				<text class="d-block text-center">{{i18n.currency}}</text>
				<text class="d-block text-center">{{i18n.applicationsNumber}}</text>
				<text class="d-block text-center">{{i18n.passesNumber}}</text>
				<text class="d-block text-center">{{i18n.timeToMarket}}</text>
			</view>
			<block v-if="list.length">
				<view class="d-grid py-20 text-center align-items-center u-border-bottom opacity-90 "
					style="grid-template-columns:1.4fr .8fr 1fr 1fr 1.4fr;" v-for="item in list">
					<view class="font-size-22">
						<text class="d-block">{{item.created_at.slice(0,10)}}</text>
						<text class="d-block">{{item.created_at.slice(10,20)}}</text>
					</view>
					<text class="font-weight-bold">{{item.name}}</text>
					<text class="font-weight-bold">{{Number(item.coin_amount)}}</text>
					<text class="font-weight-bold">{{Number(item.give_amount)}}</text>
					<view class="font-size-22">
						<text class="d-block">{{item.sell_begin.slice(0,10)}}</text>
						<text class="d-block">{{item.sell_begin.slice(10,20)}}</text>
					</view>
				</view>
			</block>
			<default-page v-else></default-page>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				page:1,
				canGet:true,
				limit:10,
				list:[],
			};
		},
		onShow() {
			this.page = 1
			this.canGet = true
			this.list = []
			this.getIEOOrder()
		},
		methods:{
			getIEOOrder(){
				if(!this.canGet) return
				const {page,limit} = this
				this.$u.api.ieo.getIEOOrder(page,limit).then(res=>{
					const list = res.message.data
					if(list.length){
						this.list = [...this.list,...list]
						this.page++
					}else{
						this.canGet = false
					}
				})
			},
		},
		computed:{
			i18n(){
				return this.$t("ieo")
			}
		},
		onReachBottom() {
			this.getIEOOrder()
		}
		
	}
</script>

<style lang="scss">

</style>
