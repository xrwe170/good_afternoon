<template>
	<view>
		<u-navbar :title="i18n.entrust" :custom-back="backTo"></u-navbar>
		<view class="p-30">
			<!-- <u-subsection :list="nav" :current="type" v-if="showNav" @change="changeNav"></u-subsection> -->
			
			
			<u-tabs :list="nav" :is-scroll="false" :current="type" v-if="showNav" @change="changeNav"></u-tabs>
			
			<view class="mt-30">
				<view class="p-20 box-shadow border-radius-20 mb-20" v-for="item in list">
					<view class="d-flex-between-center py-10 ">
						<text class="">{{i18n.tradingPair}}</text>
						<text class="font-weight-bold opacity-90">{{item.symbol}}</text>
					</view>
					<view class="d-flex-between-center py-10">
						<text class="">{{i18n.direction}}</text>
						<text class="font-weight-bold" :class="item.type == 1 ? 'text-success' : 'text-error'">{{item.type == 1 ? i18n.buy : i18n.sell}}</text>
					</view>
					<view class="d-flex-between-center py-10">
						<text class="">{{i18n.price}}</text>
						<text class="font-weight-bold  opacity-90">{{Number(item.target_price).toFixed(4)}}</text>
					</view>
					<view class="d-flex-between-center py-10">
						<text class="">{{i18n.number}}</text>
						<text class="font-weight-bold  opacity-90">{{Number(item.trade_amount)}}</text>
					</view>
					<view class="d-flex-between-center py-10" >
						<text class="">{{i18n.entrustTime}}</text>
						<text class="font-weight-bold  opacity-90">{{item.created_at}}</text>
					</view>
					<view class="d-flex-between-center py-10" >
						<text class="">{{i18n.status}}</text>
						<text class="tag-primary font-size-20" v-if="item.status == 1">{{i18n.doing}}</text>
						<text class="tag-success font-size-20" v-else-if="item.status == 2">{{i18n.complete}}</text>
					</view>
				</view>
			</view>
			<default-page v-if="!list.length"></default-page>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				page:1,
				canGet:true,
				originalList:[],
				list:[],
				limit:10,
				type:0, // 0全部，1买入，2卖出,
				showNav:true,
				from:1, //1需要navigateback，0，需要redirect
			};
		},
		onLoad(options) {
			this.from = options.from || 0
		},
		onShow() {
			this.page = 1
			this.canGet = true
			this.originalList = []
			this.list = []
			
			this.showNav = false
			setTimeout(()=>{
				this.showNav = true
			},100)
			
			this.getCoinTradeList()
		},
		methods:{
			getCoinTradeList(){
				if(!this.canGet) return
				const {page,limit} = this
				this.$u.api.market.getCoinTradeList({page,limit}).then(res=>{
					const list = res.message
					if(list.length){
						this.originalList = [...this.originalList,...list]
						this.filterList()
						this.page++
					}else{
						this.canGet = false
					}
					
				})
			},
			filterList(){
				const {type} = this
				if(type == 0){
					this.list = this.originalList
				}else{
					this.list = this.originalList.filter(item => item.type == type)
				}
			},
			changeNav(val){
				this.type = val
				this.filterList()
			},
			backTo(){
				if(this.from == 1){
					uni.navigateBack({
						delta:1
					})
				}else{
					uni.switchTab({
						url:'/pages/transaction/index'
					})
				}
			}
			
		},
		onReachBottom() {
			this.getCoinTradeList()
		},
		computed:{
			i18n(){
				return this.$t("transaction")
			},
			nav(){
				const i18n = this.$t("transaction")
				return [{
					name:i18n.all
				},{
					name:i18n.buy
				},{
					name:i18n.sell
				}]
			}
		}
		
	}
</script>

<style lang="scss">
	.box-shadow{
		box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.06);
	}
</style>
