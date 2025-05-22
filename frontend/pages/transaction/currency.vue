<template>
	<view>
		<u-navbar :custom-back="()=>{$utils.jump('/pages/transaction/index','switchTab')}">
			<view class="d-flex align-items-center font-weight-bold justify-content-center mx-auto" @click="showSelectCoin=true">
				<u-icon name="list-dot" size="40" class="position-relative" style="top: 2rpx;"></u-icon>
				<text class="font-size-36 mx-10">{{currency_name + '/' + legal_name}}</text>
			</view>
			<u-icon name="order" slot="right" size="42"  @click="$utils.jump('/pages/transaction/currency_order?from=1')">
			</u-icon>
		</u-navbar>
		<view class="px-20 py-30">
			<view class="d-flex-between-center">
				<view class="">
					<view class="d-flex align-items-center font-weight-bold">
						<text class="d-block font-size-48 mr-12 font-weight-bold"
							:style="{color:$utils.getColor(symbolQuotation.change)}">{{symbolQuotation.now_price  | setPrecision(symbolQuotation.precision_length)}}</text>
						<text v-if="symbolQuotation.change" class="tag"
							:class="symbolQuotation.change > 0 ? 'curr-tag-success' : 'curr-tag-error'">{{symbolQuotation.change}}%</text>
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
						<view class="d-flex align-items-center font-size-22 opacity-50" v-for="(item,index) in quotationNav"
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
									<text class="font-size-22 opacity-50">/{{item.legal_name}}</text>
								</text>
								<text class="d-block font-size-22 opacity-50">24H: {{Number(item.volume) | setPrecision(item.precision_length)}}</text>
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
			
			
			<view class="d-grid mt-20 align-items-center" style="grid-template-columns:1.2fr 2fr">
				<!-- 左侧交易行情 -->
				<view class="overflow-hidden" style="height: 780rpx;" v-if="buyList.length">
					<view class="d-flex-between-center font-size-22 pb-10">
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
				<view v-else class="text-center w-100 d-flex align-items-center justify-content-center"
					style="height: 804rpx;">
					<u-loading class="mx-auto" mode="flower" color="rgba(255,255,255,.8)" size="50"></u-loading>
				</view>
				
				<!-- 右侧输入框 -->
				<view class="pl-20 position-relative opacity-90" style="height: 780rpx;">
					<view class="d-grid-columns-2">
						<view class="btn btn1" :class="buyDirection == 0 ? 'active' : ''" @click="$u.throttle(()=>{buyDirection=0;number=0},500)">
							<text class="d-block">{{i18n.buy}}</text>
						</view>
						<view class="btn btn2" :class="buyDirection == 1 ? 'active' : ''" @click="$u.throttle(()=>{buyDirection=1;number=0},500)">
							<text class="d-block">{{i18n.sell}}</text>
						</view>
					</view>
					<view class="d-flex mt-20 buy-type font-size-30">
						<text class="item" :class="buyType == 1 ? 'active' : ''"
							@click="buyType=0">{{i18n.limitPrice}}</text>
						<text class="item ml-30" :class="buyType == 0 ? 'active' : ''"
							@click="buyType=1">{{i18n.marketPrice}}</text>
					</view>

					<view class="mt-20 " v-if="buyType">
						<text class="d-block">{{i18n.price}}</text>
						<input type="number" v-model="price" class="trade-input mt-10 " :placeholder="i18n.price">
					</view>


					<view class="mt-20 position-relative">
						<text class="d-block">{{i18n.jine}}</text>
						<input type="number" v-model="number" class="trade-input mt-10 " @input="changeNumber">
						<text class="position-absolute" style="bottom: 16rpx;right: 20rpx;">{{currency_name}}</text>
					</view>
					<view class="mt-20 d-grid-columns-5 text-center grid-gap-10">
						<text v-for="item in [10,25,50,75,100]" class="border-white border-radius-10 font-size-22 py-6" style="color: #999;" @click="setPercent(item)">{{item}}%</text>
					</view>
					<view class="mt-20 d-flex-between-center">
						<text class="d-block">{{i18n.balance}}</text>
						<text
							class="font-weight-bold">{{Number(balance.change_balance).toFixed(4)}}({{balance.currency_name}})</text>
					</view>
					<view class="position-absolute" style="left: 20rpx;right: 0;bottom: 0;">
						<text class="d-block mb-10">{{i18n.jye}}：{{jiaoyie}}</text>
						<button class="success-button py-0 font-size-28" v-if="buyDirection == 0" @click="confirm"
							style="height: 70rpx;line-height: 70rpx;">{{i18n.buy + '/' + i18n.long}}</button>
						<button class="error-button  py-0 font-size-28" v-else @click="confirm"
							style="height: 70rpx;line-height: 70rpx;">{{i18n.sell + '/' + i18n.short}}</button>
					</view>
				</view>
			</view>

		</view>
		<!-- 持仓列表 -->
		<u-gap height="16" class="mt-20" bg-color="#ffffff"></u-gap>
		<view class="mx-20">
			<view class="d-grid py-20 font-size-22" style="grid-template-columns:.8fr 1fr 1.4fr 1.3fr .7fr;">
				<text class="d-block text-center">{{i18n.type}}</text>
				<text class="d-block text-center">{{i18n.price}}</text>
				<text class="d-block text-center">{{i18n.number}}</text>
				<text class="d-block text-center">{{i18n.time}}</text>
				<text class="d-block text-center">{{i18n.status}}</text>
			</view>
			<block v-if="coinTradeList.length">
				<view class="d-grid py-20 text-center align-items-center border-bottom"
					style="grid-template-columns:.8fr 1fr 1.4fr 1.3fr .7fr;" v-for="item in coinTradeList">
					<text class="d-block text-center font-size-24"
						:class="item.type == 1 ? 'text-success' : 'text-error'">{{item.type == 1 ? i18n.buy : i18n.sell}}</text>
					<text class="font-weight-bold opacity-90">{{Number(item.target_price).toFixed(4)}}</text>
					<text class="font-weight-bold opacity-90">{{Number(item.trade_amount)}}</text>
					<view class="font-size-22">
						<text class="d-block">{{item.created_at.slice(0,10)}}</text>
						<text class="d-block">{{item.created_at.slice(10,20)}}</text>
					</view>
					<text class="tag-primary font-size-20" v-if="item.status == 1">{{i18n.doing}}</text>
					<text class="tag-success font-size-20" v-else-if="item.status == 2">{{i18n.complete}}</text>
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
				buyDirection: -1, //0买入，1卖出
				number: 0, //交易手数,
				buyType: 0, //0市价，1限价,
				multiple: 100, //倍数
				margin: 0, //保证金
				handlingFee: 0, //手续费,
				user_lever: 0, // 个人的资金
				showConfirmPop: false,
				price: '',
				balance: {
					change_balance: 0
				}, //余额
				percent: 0, // 百分比,
				coinTradeList:[],
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
				title:_this.$t("transaction.coins")
			})
		},
		onShow() {
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
					if (has) {
						this.symbolQuotation = has
						this.price = parseFloat(has.now_price)
						//获取交易列表
						this.getCoinTradeList(this.symbolQuotation.currency_id,this.symbolQuotation.legal_id)
						//获取余额等
						this.getWalletDetail(this.buyDirection == 1 ? has.currency_id : has.legal_id)
					}
				})
			},
			//获取余额等
			getWalletDetail(currency_id) {
				
				this.$u.api.market.getWalletDetail(currency_id, "change").then(res => {
					this.balance = res.message
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
					`/pages/transaction/currency?currency_name=${currency_name}&legal_name=${legal_name}&currency_id=${currency_id}`
				uni.redirectTo({
					url
				})
			},
			//提交
			confirm() {
				this.$u.throttle(async ()=>{
					const {
						buyDirection,
						buyType,
						price,
						number,
						symbolQuotation,
						i18n
					} = this
					if (!number || !this.$u.test.amount(number)) {
						return this.$utils.showToast(i18n.p_jine)
					}
					//如果是限价，则判断价格
					if (buyDirection == 1 && !price || !this.$u.test.amount(price)) {
						return this.$utils.showToast(i18n.p_price)
					}
					let str = ''
					let buyDirectionName = buyDirection == 0 ? i18n.buy : i18n.sell
					if (buyType == 0) {
						str = i18n.p_confirm_sj
					} else {
						str = `${i18n.p_confirm_xj_1 + price }`
					}
					str = str + buyDirectionName + i18n.p_confirm_ma
					
					const ret = await this.$utils.showModal(this.$t("common.hint"),str)
					if(!ret) return
					
					const amount = number
					const type = buyDirection == 0 ? 1 : 2
					this.$u.api.market.coinTrade(amount, price, symbolQuotation.currency_id, symbolQuotation.legal_id, type).then(res=>{
						this.number = 0
						this.$utils.showToast(this.$t("common.success"))
						this.getCoinTradeList()
						
						const iddd = this.buyDirection == 1 ? this.symbolQuotation.currency_id : this.symbolQuotation.legal_id
						this.getWalletDetail(iddd)
					})
				},1200)

			},

			//输入的数量变化
			changeNumber(e) {
				let number = Number(e.detail.value)
				const balance = Number(this.balance.change_balance)
				const {buyDirection,symbolQuotation} = this
				//如果buyDirection是买入，则将输入的数字乘以当前价格，与余额做比较
				//如果是卖出，这直接比较余额
				if(buyDirection == 0){
					//买入
					const totalPrice = number * symbolQuotation.now_price
					if(totalPrice > balance){
						number = (balance / symbolQuotation.now_price).toFixed(4)
					}
				}else if(buyDirection == 1){
					//卖出
					if (number > balance) {
						number = balance.toFixed(4)
					}
				}
				
				if (number < 0) {
					number = 0
				}
				this.number = number
				this.percent = number / balance * 100
			},
			//获取交易列表
			getCoinTradeList(currency_id,legal_id){
				this.$u.api.market.getCoinTradeList({
					currency_id,
					legal_id,
					page:1,
					limit:10
				}).then(res=>{
					this.coinTradeList = res.message || []
				})
			},
			setPercent(percent){
				const  {buyDirection,symbolQuotation,bal,buyType,price} = this
				const balance = Number(this.balance.change_balance)
				let number = 0
				if(buyDirection == 0){
					if(buyType == 0){
						number = balance * percent / 100 / symbolQuotation.now_price
					}else if(buyType == 1){
						number = balance * percent / 100 / price
					}
					
				}else if(buyDirection == 1){
					number = balance * percent / 100 
				}
				this.number = Number(number.toFixed(4))
			}
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
			jiaoyie(){
				let {number,symbolQuotation,buyType,price} = this
				number = Number(number)
				if(number <=0 || !number) number = 0
				//买入
				let ret = 0
				if(buyType == 0){
					ret = (number * symbolQuotation.now_price).toFixed(4) + symbolQuotation.legal_name
				}else{
					ret = (number * price).toFixed(4) + symbolQuotation.legal_name
				}
				return ret
			}
		},
		watch: {
			buyDirection(val) {
				//如果是0，则是买入，获取currency_id
				//如果是1，则是卖出，获取legal_id
				if(this.symbolQuotation.currency_id || this.symbolQuotation.legal_id){
					const id = val == 1 ? this.symbolQuotation.currency_id : this.symbolQuotation.legal_id
					this.getWalletDetail(id)
				}
				
			},

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
	
	.curr-tag-error{
	    background-color: red;
	}
	.tag {
	    color: #fff;
	    padding: 6rpx 12rpx;
	    border-radius: 8rpx;
	}
	
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

	.slide-button {
		width: 33rpx;
		height: 46rpx;
		border: 2rpx solid rgba(51, 51, 51, .2);
		background-color: #fff;
		border-radius: 6rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 20;

		text {
			display: block;
			width: 2rpx;
			height: 60%;
			margin: 0 2rpx;
			background-color: rgba(51, 51, 51, .2);
		}
	}

	.trade-input {
		border: 2rpx solid #eee;
		border-radius: 10rpx;
		display: block;
		padding: 10rpx 20rpx;
		font-weight: bold;

		.input-placeholder {
			color: rgba(51, 51, 51, 0.4);
			font-weight: normal;
		}
	}
	
	.border-white{
		border: 2rpx solid #999;
	}
</style>
