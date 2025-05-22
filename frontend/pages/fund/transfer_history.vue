<template>
	<view>
		<u-navbar :title="i18n.financialRecords" :borderBottom="false"></u-navbar>
		<view class="m-30">
			<view class="p-20 box-shadow border-radius-20 mb-20" v-for="item in list">
				<view class="d-flex-between-center py-10 ">
					<text class="opacity-50">{{i18n.currencyName}}</text>
					<text class="font-weight-bold" >{{item.currency_name}}</text>
				</view>
				<view class="d-flex-between-center py-10 ">
					<text class="opacity-50">{{i18n.transactionInfo}}</text>
					<text class="font-weight-bold" >{{item.transaction_info}}</text>
				</view>
				<view class="d-flex-between-center py-10 ">
					<text class="opacity-50">{{i18n.number}}</text>
					<text class="font-weight-bold" :style="{color:$utils.getColor(item.value)}">{{Number(item.value)}}</text>
				</view>
				<view class="d-flex-between-center py-10">
					<text class="opacity-50">{{i18n.time}}</text>
					<text class="font-weight-bold">{{item.created_time}}</text>
				</view>
				<view class="d-flex-between-center py-10">
					<text class="font-weight-bold">{{item.info}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				page:1,
				canGet:true,
				list:[]
			};
		},
		onShow() {
			this.page = 1
			this.canGet = true
			this.list = []
			this.getTransferHistory()
		},
		methods:{
			getTransferHistory(){
				if(!this.canGet) return
				this.$u.api.wallet.getTransferHistory(this.page).then(res=>{
					const list = res.message.data || []
					console.log(list);
					if(list.length){
						this.list = [...this.list,...list]
						this.page++
					}else{
						this.canGet = false
					}
				})
			}
		},
		computed: {
			i18n() {
				return this.$t("fund")
			}
		},
		onReachBottom() {
			this.getTransferHistory()
		}
	}
</script>

<style lang="scss">
.box-shadow {
    box-shadow: 0 0 10px 0 rgba(0, 0, 0, .1);
}
</style>
