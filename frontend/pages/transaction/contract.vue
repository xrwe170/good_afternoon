<template>
	<view>
		<u-navbar :custom-back="()=>{$utils.jump('/pages/transaction/index','switchTab')}">
			<view class="d-flex align-items-center font-weight-bold justify-content-center mx-auto" @click="showSelectCoin=true">
				<u-icon name="list-dot" size="40" class="position-relative" style="top: 2rpx;"></u-icon>
				<text class="font-size-36 mx-10">{{currency_name + '/' + legal_name}}</text>
			</view>
			<u-icon name="order" slot="right" size="42"  @click="$utils.jump(`/pages/transaction/contract_order?currency_id=${symbolQuotation.currency_id}&legal_id=${symbolQuotation.legal_id}&from=1`)">
			</u-icon>
		</u-navbar>

		<view class="px-20 py-30">
			<view class="d-flex-between-center">
				<view class="">
					<view class="d-flex align-items-center font-weight-bold">
						<text class="d-block font-size-48 mr-12 font-weight-bold"
							:style="{color:$utils.getColor(symbolQuotation.change)}">{{symbolQuotation.now_price | setPrecision(symbolQuotation.precision_length)}}</text>
						<text v-if="symbolQuotation.change" class="tag"
							:class="symbolQuotation.change > 0 ? 'tag-success' : 'tag-error'">{{symbolQuotation.change}}%</text>
					</view>
					<text class="d-block opacity-75">{{(symbolQuotation.now_price * $store.state.fiat.rate).toFixed(2)}} {{$store.state.fiat.currency_code}}</text>
				</view>
				<view class="d-flex align-items-center">
					<!-- <text class="iconfont icon-tubiao font-size-36 mr-20" @click="$utils.jump(1,'navigateBack')"></text> -->
					<u-icon name="star-fill" size="42" :color="$downColor" v-if="optionalId" @click="delOptional"></u-icon>
					<u-icon name="star" size="42" v-else @click="addOptional"></u-icon>
				</view>
			</view>
			<!-- 所有行情列表 -->
			<u-popup v-model="showSelectCoin" mode="top" length="780" contentBackgroundColor="#333333"
				:mask-custom-style="{background: 'rgba(0, 0, 0, 0.7)'}" :border-radius="0">
				<view class="status_bar"></view>
				<view class="px-30 market">
					<view class="search d-flex align-items-center py-20">
						<u-icon name="search" size="38"></u-icon>
						<input type="text" class="input" :placeholder="$t('common.search')" confirm-type="search"
							@input="filterQuotation">
					</view>
					<view class="d-grid py-20" style="grid-template-columns:1.1fr 1fr 1fr;">
						<view class="d-flex align-items-center font-size-22 " v-for="(item,index) in quotationNav"
							:class="item.align">
							<text>{{item.name}}</text>
						</view>
					</view>
					<scroll-view scroll-y="true" style="height: 600rpx;">
						<view class="market-item" v-for="item in quotation"
							@click="jump(item.currency_name,item.legal_name,item.currency_id)">
							<view class="left">
								<text class="d-block">
									<text class="font-size-28 font-weight-bold">{{item.currency_name}}</text>
									<text class="font-size-22 ">/{{item.legal_name}}</text>
								</text>
								<text class="d-block font-size-22 ">24H: {{Number(item.volume) | setPrecision(item.precision_length)}}</text>
							</view>
							<view class="text-center">
								<text class="d-block font-size-30 font-weight-bold">{{item.now_price | setPrecision(item.precision_length)}}</text>
							</view>
							<view class="">
								<view class="right" :style="{backgroundColor:$utils.getColor(item.change)}">
									{{item.change + '%'}}
								</view>
							</view>
						</view>
					</scroll-view>
			
				</view>
			</u-popup>
			
			<!-- 买入卖出和实时买卖 -->
			<view class="d-grid mt-20 align-items-center" style="grid-template-columns:2fr 1.2fr ">
				<!-- 买入卖出 -->
				<view class="pr-20 position-relative opacity-90" style="height: 780rpx;">
					<view class="d-grid-columns-2">
						<view class="btn btn1" :class="buyDirection == 0 ? 'active' : ''" @click="buyDirection=0">
							<text class="d-block">{{i18n.buy}}</text>
						</view>
						<view class="btn btn2" :class="buyDirection == 1 ? 'active' : ''" @click="buyDirection=1">
							<text class="d-block">{{i18n.sell}}</text>
						</view>
					</view>
					<view class="d-flex mt-20 buy-type font-size-30">
						<text class="item" :class="buyType == 0 ? 'active' : ''"
							@click="buyType=0">{{i18n.marketPrice}}</text>
						<text class="item ml-30" :class="buyType == 1 ? 'active' : ''"
							@click="buyType=1">{{i18n.limitPrice}}</text>
					</view>
					<view class="mt-20" v-if="buyType == 1">
						<input type="number" v-model="price" class="trade-input mt-10 " :placeholder="i18n.price">
					</view>
					<view class="mt-20">
						<view class="d-flex-between-center">
							<text class="d-block">{{i18n.number}}</text>
							<text class="tag  font-size-24 ml-20 px-20 py-2" :class="buyDirection==0 ? 'tag-success tag-plain-success' : 'tag-error tag-plain-error'">{{number}}</text>
						</view>
						<u-slider v-model="number" :min="1" :max="100" class="mt-32" :active-color="buyDirection==1 ? $downColor : $upColor"
							:use-slot="true">
							<view class="">
								<view class="slide-button">
									<text></text>
									<text></text>
									<text></text>
								</view>
							</view>
						</u-slider>
					</view>
					<view class="mt-46">
						<text class="d-block">{{i18n.multiple}}</text>
						<view class="d-flex mt-16" v-if="buyDirection==0">
							<view class="" v-for="item in muit">
								<text class="tag  mr-16 px-20 font-size-30" :class="multiple == item ? 'tag-success' : 'tag-plain-success'" @click="multiple=item">{{item}}</text>
							</view>
						</view>
						<view class="d-flex mt-16" v-else>
							<view class="" v-for="item in muit">
								<text class="tag  mr-16 px-20 font-size-30" :class="multiple == item ? 'tag-error' : 'tag-plain-error'" @click="multiple=item">{{item}}</text>
							</view>
						</view>
					</view>
					<view class="mt-26 d-flex-between-center">
						<text class="d-block">{{i18n.margin}}</text>
						<text>{{margin}}</text>
					</view>
					<view class="mt-12 d-flex-between-center">
						<text class="d-block">{{i18n.handlingFee}}</text>
						<text>{{handlingFee}}</text>
					</view>
					<view class="mt-12 d-flex-between-center">
						<text class="d-block">{{i18n.balance}}</text>
						<view class="d-flex align-items-center">
							<text>{{user_lever}}USDT</text>
						</view>
					</view>
					
					<!-- <number-box :precision="4"></number-box> -->
					
					<view class="position-absolute" style="left: 0;right: 20rpx;bottom: 0;">
						<button class="success-button mt-60 py-0 font-size-28" v-if="buyDirection == 0" @click="showConfirmPop = true" style="height: 70rpx;line-height: 70rpx;">{{i18n.buy + '/' + i18n.long}}</button>
						<button class="error-button mt-60 py-0 font-size-28" v-else @click="showConfirmPop = true" style="height: 70rpx;line-height: 70rpx;">{{i18n.sell + '/' + i18n.short}}</button>
					</view>
				</view>
				<!-- 实时购买 -->
				<view class="overflow-hidden" style="height: 780rpx;" v-if="buyList.length">
					<view class="d-flex-between-center font-size-22  py-10">
						<text>{{i18n.price}}({{currency_name}})</text>
						<text>{{i18n.amount}}({{legal_name}})</text>
					</view>
					<view class="">
						<view class="d-flex-between-center px-10 py-6 position-relative font-size-22"
							v-for="item in buyList.slice(0,10)">
							<text :style="{color:$downColor}">{{item[0]}}</text>
							<text class="opacity-75">{{item[1]}}</text>
							
							<view class="position-absolute opacity-20" style="top: 0;bottom: 0;right: 0;"
								:style="{background:$downColor,width:(item[1]/buyListCount*100*10+'%')}"></view>
						</view>
					</view>
					<view class="">
						<view class="d-flex-between-center px-10 py-6 position-relative font-size-22"
							v-for="item in sellList.slice(0,10)">
							<text :style="{color:$upColor}">{{item[0]}}</text>
							<text class="opacity-75">{{item[1]}}</text>
							<view class="position-absolute opacity-20" style="top: 0;bottom: 0;right: 0;"
								:style="{background:$upColor,width:(item[1]/sellListCount*100*10+'%')}"></view>
						</view>
					</view>
				</view>
				<view v-else class="text-center w-100 d-flex align-items-center justify-content-center" style="height: 804rpx;">
					<u-loading class="mx-auto" mode="flower" color="rgba(255,255,255,.8)" size="50"></u-loading>
				</view>
				
			</view>
			
			<u-popup v-model="showConfirmPop" border-radius="10" length="90%">
				<view class="p-30">
					<text class="d-block font-size-30 font-weight-bold">{{buyDirection ? i18n.sell : i18n.buy}}</text>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="">{{i18n.tradingPair}}</text>
						<text class="font-weight-bold">{{currency_name + '/' + legal_name}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="">{{i18n.direction}}</text>
						<text class="font-weight-bold" :class="buyDirection ? 'text-error' : 'text-success'">{{buyDirection ? i18n.sell : i18n.buy}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="">{{i18n.number}}</text>
						<text class="font-weight-bold">{{number}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="">{{i18n.multiple}}</text>
						<text class="font-weight-bold">{{multiple}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="">{{i18n.handlingFee}}</text>
						<text class="font-weight-bold">{{handlingFee}}</text>
					</view>
					<view class="d-flex-between-center border-bottom-white py-16">
						<text class="">{{i18n.margin}}</text>
						<text class="font-weight-bold">{{margin}}</text>
					</view>
					<view class="d-flex-between-center mt-30">
						<button class="secondary-button font-size-28 w-48 py-0" @click="showConfirmPop = false">{{$t("common.cancel")}}</button>
						<button class="w-48 font-size-28 py-0" @click="confirm"  :class="buyDirection ? 'error-button' : 'success-button'">{{$t("common.confirm")}}</button>
					</view>
				</view>
			</u-popup>
		</view>
		<!-- 持仓列表 -->
		<u-gap height="16" class="mt-20" bg-color="#FFFFFF"></u-gap>
		<view class="">
			<view class="d-grid py-20 font-size-22" style="grid-template-columns:1fr 1fr 1.4fr .7fr 1.2fr;">
				<text class="d-block text-center">{{i18n.type}}</text>
				<text class="d-block text-center">{{i18n.time}}</text>
				<text class="d-block text-center">{{i18n.price}}</text>
				<text class="d-block text-center">{{i18n.number}}</text>
				<text class="d-block text-center">{{i18n.operation}}</text>
			</view>
			<block v-if="positionList.length">
				<view class="d-grid py-20 text-center align-items-center border-bottom" style="grid-template-columns:1fr 1fr 1.4fr .7fr 1.2fr;" v-for="item in positionList">
					<text class="d-block text-center" :class="item.type == 1 ? 'text-success' : 'text-error'">{{item.type == 1 ? i18n.buy : i18n.sell}}</text>
					<view class="font-size-22 opacity-90">
						<text class="d-block">{{item.transaction_time.slice(0,10)}}</text>
						<text class="d-block">{{item.transaction_time.slice(10,20)}}</text>
					</view>
					<text class="font-weight-bold opacity-90">{{Number(item.price).toFixed(4)}}</text>
					<text class="opacity-90">{{item.share}}</text>
					<view class="w-50 mx-auto">
						<text class="tag tag-success" v-if="item.order_type == 2" @click="selfHold(item.id)">{{i18n.transSelfHold}}</text>
						<text class="tag tag-primary " v-else @click="cover(item.id)">{{i18n.cover}}</text>
						
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
				currency_name: '',
				legal_name: '',
				currency_id: 0,
				symbolQuotation: {},
				// 交易数据
				buyList: [],
				sellList: [],
				buyListCount: 0,
				sellListCount: 0,
				optionalId: 0,
				showSelectCoin: false,
				//行情
				originalQuotation: [],
				quotation: [],
				buyDirection: 0, //0买入，1卖出
				number: 1, //交易手数,
				buyType: 0, //0市价，1限价,
				multiple:100, //倍数
				balance:0,//余额
				margin:0,//保证金
				handlingFee:0,//手续费,
				user_lever:0,// 个人的资金
				showConfirmPop:false,
				price:'',
				positionList:[],
				muit:[100,150,200], //倍数
			};
		},
		onLoad(options) {
			let {
				currency_name,
				legal_name,
				currency_id,
				buy_direction
			} = options
			this.currency_name = currency_name || 'BTC'
			this.legal_name = legal_name || 'USDT'
			this.currency_id = currency_id || 1
			buy_direction = buy_direction || 0
			this.buyDirection = Number(buy_direction)
			
			const _this = this
			uni.setNavigationBarTitle({
				title:_this.$t("home.futures")
			})
		},
		onShow() {
			uni.showTabBar()
			this.startSocket()
			//查看是否已添加自选
			this.checkOptional()

			this.getQuotation()
		},
		methods: {
			// 获取所有行情
			getQuotation() {
				this.$u.api.index.getQuotation().then(res => {
					this.originalQuotation = res.message[0].quotation.map(item=>{
						item.precision_length = this.$utils.getPrecisionLength(item.now_price)
						return item
					})
					this.quotation = this.originalQuotation
					
					const has = this.originalQuotation.find(item => item.currency_id == this.currency_id)
					if(has){
						this.symbolQuotation = has
						this.price = has.now_price
						//获取杠杆的一些信息和获取持仓列表
						this.getLeverDeal(has.currency_id,has.legal_id)
					}
				})
			},
			//获取杠杆的一些信息和持仓列表
			getLeverDeal(currency_id,legal_id){
				this.$u.api.market.getLeverDeal(currency_id,legal_id).then(res=>{
					let user_lever = res.message.user_lever || 0
					this.user_lever = Number(user_lever).toFixed(4)
					this.muit = res.message.multiple.muit.map(item => Number(item.value))
					const list = res.message.my_transaction || []
					this.positionList = list
				})
			},
			startSocket() {
				const _this = this
				const {
					currency_id,
				} = this
				
				this.$store.state.socket.on('daymarket', res => {
					let data = res
					const has = this.originalQuotation.findIndex(item => item.currency_name == data
						.currency_name && item
						.legal_name == data.legal_name)
					if (has > -1) {
						let obj = {
							...this.originalQuotation[has],
							...data,
						}
						this.originalQuotation.splice(has, 1, obj)
					}

					if (this.currency_name == res.currency_name && this.legal_name == res.legal_name) {
						this.symbolQuotation = {
							...this.symbolQuotation,
							...res
						}
					}
				});
				this.$store.state.socket.on('market_depth', res => {
					this.getMarketDepthData(res)
				});
			},
			filterQuotation(e) {
				let val = e.detail.value
				if (!val) {
					this.quotation = this.originalQuotation
				} else {
					this.quotation = this.originalQuotation.filter(el => {
						val = val.toLowerCase()
						let currency_name = el.currency_name.toLowerCase()
						let legal_name = el.legal_name.toLowerCase()
						if (currency_name.indexOf(val) > -1 || legal_name.indexOf(val) > -1) {
							return el
						}
					})
				}
			},
			//接受市场数据
			getMarketDepthData(data) {
				if (this.currency_name == data.currency_name && this.legal_name == data.legal_name) {
					if (typeof(data.bids) == 'string') {
						this.buyList = JSON.parse(data.bids);
						this.sellList = JSON.parse(data.asks).reverse();
					} else {
						this.buyList = data.bids;
						this.sellList = data.asks.reverse();
					}

					this.buyListCount = this.buyList.reduce((total, item) => {
						return total + item[1] * 1
					}, 0);

					this.sellListCount = this.sellList.reduce((total, item) => {
						return total + item[1] * 1
					}, 0);
				}
			},
			//查看是否已添加自选
			async checkOptional() {
				const ret = await this.$u.api.market.getOptionalList()
				const item = ret.message.find(item => item.currency_id == this.currency_id)
				if (item) {
					this.optionalId = item.id
				}
			},
			//添加自选
			addOptional() {
				this.$u.throttle(()=>{
					const {
						currency_id,
						i18n
					} = this
					this.$u.api.market.addOptional(currency_id).then(res => {
						this.optionalId = res.message.id
						this.$utils.showToast(i18n.addOptionalSuccess)
					})
				},1200)
			},
			// 删除自选
			delOptional() {
				this.$u.throttle(()=>{
					const {
						optionalId,
						i18n
					} = this
					this.$u.api.market.delOptional(optionalId).then(res => {
						this.optionalId = 0
						this.$utils.showToast(i18n.delOptionalSuccess)
					})
				},1200)
			},
			// 跳转
			jump(currency_name, legal_name, currency_id) {
				if (currency_name == this.currency_name && legal_name == this.legal_name && currency_id == this
					.currency_id) this.showSelectCoin = false
				const url =
					`/pages/transaction/contract?currency_name=${currency_name}&legal_name=${legal_name}&currency_id=${currency_id}`
				uni.reLaunch({
					url,
				})
			},
			confirm(){
				this.$u.throttle(()=>{
					const {number,multiple,symbolQuotation,buyDirection,buyType,i18n} = this
					const {currency_id,legal_id} = symbolQuotation
					let price = this.price
					if(buyType == 0){
						price = '999'
					}
					if((!price || !this.$u.test.amount(price)) && buyType == 1){
						console.log(this.$u.test.amount(price));
						this.$utils.showToast(i18n.plsIptCrtPrice)
						return false
					}
					
					this.$u.api.market.submitLever({
						share:number,
						multiple,
						legal_id,
						currency_id,
						type:buyDirection == 0 ? 1 : 2,
						status:buyType == 0 ? 1 : 0,
						target_price:price,
						password:''
					}).then(res=>{
						this.showConfirmPop = false
						this.$utils.showToast(res.message,'success')
						
						this.getLeverDeal(this.symbolQuotation.currency_id,this.symbolQuotation.legal_id)
					})
				},1200)
			},
			//平仓
			cover(id){
				this.$u.throttle(async ()=>{
					const {i18n} = this
					const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmCover)
					if(!ret) return
					this.$u.api.market.cover(id).then(res=>{
						this.$utils.showToast(res.message)
						this.getLeverDeal(this.symbolQuotation.currency_id,this.symbolQuotation.legal_id)
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
						this.$utils.showToast(res.message)
						this.getLeverDeal(this.symbolQuotation.currency_id,this.symbolQuotation.legal_id)
					})
				},1200)
			},
			//计算保证金手续费
			calc(){
				const {symbolQuotation,buyDirection,multiple,number} = this
				const spread = Number(symbolQuotation.spread)
				const lever_share_num = Number(symbolQuotation.lever_share_num)
				const lever_trade_fee = Number(symbolQuotation.lever_trade_fee)
				const prices  = Number(symbolQuotation.now_price)
				
				let spreadPrices = parseFloat((prices * spread) / 100);
				
				let pricesTotal = 0;
				if (buyDirection == 0) {
					// 如果方向是买
					pricesTotal = parseFloat(prices + spreadPrices);
				} else {
					pricesTotal = parseFloat(prices - spreadPrices);
				}
				
				let totalPrice = parseFloat(pricesTotal * lever_share_num * number);
				this.margin = (totalPrice / multiple).toFixed(4);
				this.handlingFee = parseFloat((totalPrice * lever_trade_fee) / 100).toFixed(4);
			},
		},
		computed: {
			i18n() {
				return this.$t("transaction")
			},
			symbol() {
				return this.currency_name + '/' + this.legal_name
			},
			quotationNav(){
				const i18n = this.$t("transaction")
				return [{
					name: i18n.tradingPair,
					align: ''
				},
				{
					name: i18n.lastPrice,
					align: 'justify-content-center'
				},
				{
					name: i18n.todayChange,
					align: 'justify-content-end'
				}]
			},
			
		},
		watch:{
			multiple(){
				this.calc()
			},
			number(){
				this.calc()	
			},
			'symbolQuotation.now_price'(){
				this.calc()	
			}
		},
		onHide(){
			this.$store.state.socket.removeListener('daymarket')
			this.$store.state.socket.removeListener('market_depth')
		},
		onUnload() {
			this.$store.state.socket.removeListener('daymarket')
			this.$store.state.socket.removeListener('market_depth')
		}
	}
</script>

<style lang="scss" scoped>
	.market {
		color: rgba(255, 255, 255, .7);
	
		.search {
			border-bottom: 2rpx solid rgba(255, 255, 255, .1);
	
			.input {
				height: 38rpx;
				flex: 1;
	
				.input-placeholder {
					color: #fff;
				}
			}
		}
	
		.market-item {
			overflow: hidden;
			padding: 20rpx 0;
			border-top: 2rpx solid rgba(255, 255, 255, .1);
			align-items: center;
			display: grid;
			grid-template-columns: 1.1fr 1fr 1fr;
	
			.right {
				margin-left: auto;
				width: 120rpx;
				height: 50rpx;
				line-height: 50rpx;
				text-align: center;
				border-radius: 10rpx;
				background-color: #15be97;
				color: #fff;
				font-size: 24rpx;
			}
		}
	}

	.btn {

		text-align: center;
		padding: 14rpx 0;
		background-size: 100% 100%;
		color: rgba(255, 255, 255, .9);
		border-radius: 8rpx;

		&.btn1 {
			background-image: url('../../static/image/icon/k-icon-1-none.png');

			&.active {
				background-image: url('../../static/image/icon/k-icon-1.png');
			}
		}

		&.btn2 {
			background-image: url('../../static/image/icon/k-icon-2-none.png');

			&.active {
				background-image: url('../../static/image/icon/k-icon-2.png');
			}
		}
	}

	.buy-type {
		.item {
			&::after {
				display: block;
				content: "";
				width: 70%;
				background-color: $uni-color-333;
				margin: 0 auto;
				height: 4rpx;
				border-radius: 6rpx;
				margin-top: 4rpx;
				transition: all .3s ease 0s;
			}

			&.active {
				&::after {
					background-color: #fff;
				}
			}
		}
	}
	
	.slide-button{
		width: 33rpx;
		height: 46rpx;
		border: 2rpx solid rgba(51,51,51,.2);
		background-color: #fff;
		border-radius: 6rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 20;
		text{
			display: block;
			width: 2rpx;
			height: 60%;
			margin: 0 2rpx;
			background-color: rgba(51,51,51,.2);
		}
	}
	
	.trade-input{
		border: 2rpx solid #eee;
		border-radius: 10rpx;
		display: block;
		padding: 10rpx 20rpx;
		font-weight: bold;
		.input-placeholder{
			color: rgba(51, 51, 51, 0.4);
			font-weight: normal;
		}
	}
</style>
