<template>
	<view>
		<u-navbar :title="i18n.delegateList" ></u-navbar>
		<u-sticky>
			<!-- <view class="p-30 position-relative"  style="background-color: #E1E1EA;">
				<view class="d-flex align-items-center">
					<text class="d-block font-size-22 opacity-75">{{i18n.riskRate}}:</text>
					<text class="ml-12 font-weight-bold">{{(Number(info.hazard_rate) / 1000000).toFixed(2)}}%</text>
				</view>
				<view class="d-flex align-items-center">
					<text class="d-block font-size-22 opacity-75">{{i18n.totalPandL}}:</text>
					<text class="ml-12 font-weight-bold" :style="{color:$utils.getColor(info.profits_all)}" >{{Number(info.profits_all).toFixed(4)}}</text>
				</view>
				<button class="error-button position-absolute font-size-22 py-0 px-70" style="right: 30rpx;top: 36rpx;" @click="showCoverAll=true">{{i18n.oneClickCover}}</button>
			</view> -->
			
			<view class="u-sticky" style="position: static; top: 44px; width: 414px; z-index: 970;">
				<view class="position-relative con-bg d-flex align-items-center justify-content-between">
					<view>
						<view class="d-flex align-items-center">
							<text class="d-block font-size-22">{{i18n.riskRate}}:</text>
							<text class="ml-12 font-weight-bold">{{(Number(info.hazard_rate) / 1000000).toFixed(2)}}%</text>
						</view>
						<view class="d-flex align-items-center">
							<text class="d-block font-size-22">{{i18n.totalPandL}}:</text>
							<text class="ml-12 font-weight-bold" :style="{color:$utils.getColor(info.profits_all)}" >{{Number(info.profits_all).toFixed(4)}}</text>
						</view>
					</view>
					<button class="con-button" @click="showCoverAll=true">
						{{i18n.oneClickCover}}
					</button>
				</view>
			</view>
			
			<view class="p-30">
				<u-tabs :list="nav" :is-scroll="false" :current="current"  @change="changeNav"></u-tabs>
			</view>
		</u-sticky>
		<view class="list">
			<view class="p-30 border-bottom" v-for="item in list">
				<view class="d-flex">
					<view class="d-flex align-items-center">
						<text class="tag py-0" :class="item.type == 1 ? 'tag-success' : 'tag-error'">{{item.type == 1 ? i18n.buy : i18n.sell}}</text>
						<text class="ml-8">{{item.symbol}}</text>
						<text class="ml-8">x{{item.share + i18n.lots}}</text>
						<text class="ml-8">x{{item.multiple+i18n.multiple}}</text>
					</view>
				</view>
				<view class="d-grid-columns-3 mt-20" >
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.open}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8 opacity-90">{{Number(item.price).toFixed(4)}}</text>
					</view>
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.targetProfitPrice}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8  opacity-90">{{Number(item.target_profit_price).toFixed(4)}}</text>
					</view>
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.updatePrice}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8  opacity-90">{{Number(item.update_price).toFixed(4)}}</text>
					</view>
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.stopLossPrice}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8  opacity-90">{{Number(item.stop_loss_price).toFixed(4)}}</text>
					</view>
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.margin}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8  opacity-90">{{Number(item.caution_money).toFixed(4)}}</text>
					</view>
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.overnightMoney}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8  opacity-90">{{Number(item.overnight_money).toFixed(4)}}</text>
					</view>
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.handlingFee}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8  opacity-90">{{Number(item.trade_fee).toFixed(4)}}</text>
					</view>
					<view class="my-8">
						<text class="d-block font-size-22 opacity-50">{{i18n.openTime}}</text>
						<view class="d-block font-size-26 font-weight-bold mt-8  opacity-90">
							<text class="d-block">{{item.transaction_time.slice(0,10)}}</text>
							<text class="d-block">{{item.transaction_time.slice(10,20)}}</text>
						</view>
					</view>
					<view class="my-8" v-if="item.status == 3">
						<text class="d-block font-size-22 opacity-50">{{i18n.totalPandL}}</text>
						<text class="d-block font-size-30 font-weight-bold mt-8 opacity-90" :style="{color:$utils.getColor(item.profits)}">{{Number(item.profits).toFixed(4)}}</text>
					</view>
					
				</view>
				<view class="d-flex align-items-center mt-20" style="flex-direction: row-reverse;" v-if="item.status == 1">
					<button class="error-button font-size-22 py-0 px-70 mx-0" v-if="item.order_type == 1" @click="cover(item.id)">{{i18n.cover}}</button>
					<button class="warning-button font-size-22 py-0 px-70 mx-0" v-else-if="item.order_type == 2" @click="selfHold(item.id)">{{i18n.transSelfHold}}</button>
					<button class="primary-button font-size-22 py-0 px-70 mr-0 mr-20" @click="editFunc(item)">{{i18n.setProfitLoss}}</button>
				</view>
			</view>
			
			<default-page v-if="!list.length"></default-page>
			
			<!-- 设置止盈止损 -->
			<u-popup v-model="showEdit" border-radius="10" length="90%">
				<view class="p-30">
					<text class="d-block text-center font-size-32">{{i18n.setProfitLoss}}</text>
					<view class="d-flex-between-center mt-20">
						<text>{{i18n.targetProfitPrice}}</text>
						<view class="">
							<u-number-box v-model="profitPrice" :positive-integer="false" input-width="140" size="22"></u-number-box>
							<text class="d-block font-size-22 mt-8 opacity-50">{{i18n.expectedProfit + ':' + expectedProfit}}</text>
						</view>
					
					</view>
					<view class="d-flex-between-center mt-20">
						<text>{{i18n.stopLossPrice}}</text>
						<view class="">
							<u-number-box v-model="lossPrice" :positive-integer="false" input-width="140" size="22"></u-number-box>
							<text class="d-block font-size-22 mt-8 opacity-50">{{i18n.expectedLoss + ':' + expectedLoss}}</text>
						</view>
					</view>
					<view class="d-flex-between-center mt-30">
						<button class="secondary-button font-size-24 w-48 py-0" @click="showEdit = false">{{$t("common.cancel")}}</button>
						<button class="w-48 primary-button font-size-24 py-0" @click="confirm" >{{$t("common.confirm")}}</button>
					</view>
				</view>
			</u-popup>	
			
			<!-- 一键平仓 -->
			<u-action-sheet :list="coverNav" v-model="showCoverAll" @click="coverAllFunc"></u-action-sheet>
			
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				currency_id:0,
				legal_id:0,
				page:1,
				canGet:true,
				list:[],
				info:{
					hazard_rate:0,
					profits_all:0
				},
				editId:0,
				showEdit:false,
				currentItem:{},
				profitPrice:0,//设置的止盈价
				expectedProfit:0,//预期止盈价
				lossPrice:0,//设置的止损价
				expectedLoss:0,//预期亏损,
				showCoverAll:false,
				from:1, //1需要navigateback，0，需要redirect
				current:0,
				status:1
			};
		},
		onLoad(options) {
			const {
				legal_id,
				currency_id,
				from
			} = options
			if(!legal_id || !currency_id){
				this.$utils.showToast(this.$t("common.paramsWrong"))
				setTimeout(()=>{
					uni.switchTab({
						url:'/pages/trade/trade'
					})
				},1200)
				return
			}
			this.legal_id = legal_id || 3
			this.currency_id = currency_id || 1
			this.from = from || 0
			
			this.startSocket()
		},
		onShow() {
			this.canGet = true
			this.page = 1
			this.list = []
			this.getLeverDealByPage()
		},
		methods:{
			getLeverDealByPage(){
				if(!this.canGet) return
				const {currency_id,legal_id,page,status} = this
				this.$u.api.market.getleverTrade(currency_id,legal_id,page,status).then(res=>{
					res = res.message
					let order = res.message.data
					this.info = res.rate_profits_total
					this.info.profits_all = this.info.profits_total
					if(order.length){
						this.list = [...this.list,...order]
						this.page++
					}else{
						this.canGet = false
					}					
				})
			},
			changeNav(e){
				this.current = e
				if(e == 0) this.status = 1
				if(e == 1) this.status = 3
				this.canGet = true
				this.page = 1
				this.list = []
				this.getLeverDealByPage()
			},
			async startSocket(){
				const _this = this
				let ret = await this.$u.api.setting.getUserInfo()
				ret = ret.message
				
				this.$store.state.socket.emit('login', ret.id);
				this.$store.state.socket.on('lever_trade', res => {
					this.info = {
						...this.info,
						...res
					}
				});
				this.$store.state.socket.on('lever_closed', res => {
					const ids = res.id || []
					ids.forEach(item => {
						const has = this.list.findIndex(el => el.id == item)
						if(has > -1) this.list.splice(has,1) 
					})
				});
				this.$store.state.socket.on('daymarket', data => {
					if (this.currency_id == data.currency_id && this.legal_id == data.legal_id) {
						this.list.forEach(item=>{
							item.update_price = data.now_price
						})
					}
				});
			},
			//平仓
			cover(id){
				this.$u.throttle(async ()=>{
					const {i18n} = this
					const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmCover)
					if(!ret) return
					this.$u.api.market.cover(id).then(res=>{
						const has = this.list.findIndex(item => item.id == id)
						this.list.splice(has,1)
						this.$utils.showToast(res.message)
					})
				},1200)
			},
			//转自持
			selfHold(id){
				this.$u.throttle(async ()=>{
					const {i18n} = this
					const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmSelfHold)
					if(!ret) return
					this.$u.api.follow.selfHold(id).then(res=>{
						const has = this.list.findIndex(item => item.id == id)
						let item = this.list[has]
						item.order_type = 1
						this.list.splice(has,1,item)
						
						this.$utils.showToast(res.message)
					})
				},1200)
			},
			//设置止盈止损
			editFunc(item){
				this.currentItem = item
				this.editId = item.id
				//设置止盈和预期盈利
				this.profitPrice = item.target_profit_price > 0 ? Number(item.target_profit_price)  : Number(item.update_price);

				//设置止损和预期亏损
				this.lossPrice = item.stop_loss_price > 0 ? Number(item.stop_loss_price)  : Number(item.update_price);

				this.showEdit = true
			},
			//提交修改止盈止损
			confirm(){
				const {currentItem,profitPrice,lossPrice} = this
				this.$u.api.market.setStop(currentItem.id,profitPrice,lossPrice).then(res=>{
					this.$utils.showToast(res.message)
					const has = this.list.findIndex(item => item.id == currentItem.id)
					let item = this.list[has]
					item.target_profit_price = profitPrice
					item.stop_loss_price = lossPrice
					this.list.splice(has,1,item)
					this.showEdit = false
				})
			},
			//全部平仓
			coverAllFunc(type){
				this.$u.throttle(async ()=>{
					const {i18n} = this
					let str = this.$t("common.confirm") + i18n.coverAll + '?'
					if(type == 1){
						str = this.$t("common.confirm") + i18n.onlyCoverMany + '?'
					}else if(type == 2){
						str = this.$t("common.confirm") + i18n.onlyCoverEmpty + '?'
					}
					const ret = await this.$utils.showModal(this.$t("common.hint"),str)
					if(!ret) return
					
					this.$u.api.market.coverAll(type).then(res=>{
						this.$utils.showToast(res.message)
						this.list = []
						this.canGet = true
						this.page = 1
						this.getLeverDealByPage()
					})
				},1200)
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
		computed:{
			i18n(){
				return this.$t("transaction")
			},
			coverNav(){
				return [{
					text:this.$t("transaction.coverAll")
				},{
					text:this.$t("transaction.onlyCoverMany")
				},{
					text:this.$t("transaction.onlyCoverEmpty")
				}]
			},
			nav(){
				return [{
					name:this.$t("transaction.position")
				},{
					name:this.$t("transaction.cover")
				}]
			}
		},
		onReachBottom() {
			this.getLeverDealByPage()
		},
		watch:{
			profitPrice(){
				let expectedProfit = 0
				const {currentItem,profitPrice} = this
				if(item.type == 1){
					if(profitPrice > item.price){
						expectedProfit = (profitPrice - item.price) * item.share;
					}else{
						expectedProfit = '0.00';
					}
					
				}else{
					if(item.price > profitPrice){
						expectedProfit = (item.price -profitPrice) * item.share;
					}else{
						expectedProfit ='0.00';
					}
					
				}
				this.profitPrice = profitPrice.toFixed(4)
			},
			lossPrice(){
				let expectedLoss = 0
				const {currentItem,lossPrice} = this
				
				if(currentItem.type == 1){
					if(currentItem.price > lossPrice){
						expectedLoss = (currentItem.price -lossPrice) * currentItem.share;
					}else{
						expectedLoss = '0.00';
					}
					
				}else{
					if(lossPrice > currentItem.price){
						expectedLoss = (lossPrice - currentItem.price) * currentItem.share;
					}else{
						expectedLoss = '0.00';
					}
				}
				this.expectedLoss = expectedLoss.toFixed(4)
			}
		},
		onHide(){
			this.$store.state.socket.removeListener('lever_trade')
			this.$store.state.socket.removeListener('lever_closed')
			this.$store.state.socket.removeListener('daymarket')
		},
		onUnload() {
			this.$store.state.socket.removeListener('lever_trade')
			this.$store.state.socket.removeListener('lever_closed')
			this.$store.state.socket.removeListener('daymarket')
		}
	}
</script>

<style lang="scss">
.u-sticky {
	background-color: #2e5cd1;
	.con-bg{
		color: #fff;
		padding: 29rpx 0 29rpx 29rpx;
	}
	.con-button {
		background: #fff;
		font-size: 25rpx;
		font-family: Source Han Sans CN;
		font-weight: 500;
		color: #2e5cd1;
		margin: 0;
		border-radius: 50rpx 0px 0 50rpx;
	}
}
.border-bottom{
	border-bottom: 2rpx solid #dddddd;
}
</style>
