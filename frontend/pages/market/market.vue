<template>
	<view class="wrapper">
		<view class="status_bar"></view>
		<view class="mx-30 py-30 d-flex-between-center">
			
			<u-subsection :list="navs" :current="currentNav" :afterWidth="77" style="flex:1" @change="navChangeFunc"></u-subsection>
			<!-- <u-icon name="search" size="40" color="#ffffff" class="ml-30" @click="$utils.jump('/pages/market/search')"></u-icon> -->
			<image src="/static/image/img/search.png" style="width: 46rpx; height: 46rpx;" @click="$utils.jump('/pages/market/search')"></image>
		</view>
		<view class="m-30 market text-white">
			<view class="mt-22" v-show="currentNav == 0">
				<view class="title d-grid" style="grid-template-columns:1.1fr 1fr 1fr;">
					<view class="d-flex align-items-center nas-title" v-for="(item,index) in quotationNav" :class="item.align">
						<text>{{item.name}}</text>
						<!-- <image  v-if="index!=0" class="pai-xu" src="/static/image/img/paixu.png"></image> -->
					</view>
				</view>
				<navigator class="market-item" v-for="item in optionalList" open-type="reLaunch" :url="`/pages/transaction/index?from=market&currency_name=${item.currency_name}&legal_name=${item.legal_name}&currency_id=${item.currency_id}`">
					<view class="left m-left">
						<text class="d-block">
							<text>{{item.currency_name}}</text>
							<text>/{{item.legal_name}}</text>
						</text>
						<text class="d-block">24H: {{Number(item.volume) | setPrecision(item.precision_length)}}</text>
					</view>
					<view class="text-center m-content">
						<text class="d-block"
							:style="{color:$utils.getColor(item.change)}">{{Number(item.now_price)  | setPrecision(item.precision_length)}}</text>
						<text class="d-block" v-if="$store.state.fiat.currency_code != 'USD'">
							{{(item.now_price * $store.state.fiat.rate)  | setPrecision(item.precision_length) }} {{$store.state.fiat.currency_code}}
						</text>
					</view>
					<view class="d-flex justify-content-end m-right">
						<view :style="{backgroundColor:$utils.getColor(item.change)}">
							{{item.change + '%'}}
						</view>
					</view>
				</navigator>
				<default-page v-if="!optionalList.length"></default-page>
			</view>
			<view class="mt-22" v-show="currentNav == 1">
				<view class="title d-grid" style="grid-template-columns:1.1fr 1fr 1fr;">
					<view class="d-flex align-items-center nas-title" v-for="(item,index) in quotationNav"
						@click="sort(index)" :class="item.align">
						<text>{{item.name}}</text>
						<image :src="item.sort | sort2Icon" class="ml-8" style="width: 13.6rpx; height: 25.6rpx;">
						</image>
					</view>
				</view>
				<navigator class="market-item" v-for="item in quotation" open-type="reLaunch" :url="`/pages/transaction/index?from=market&currency_name=${item.currency_name}&legal_name=${item.legal_name}&currency_id=${item.currency_id}`">
					<view class="left m-left">
						<text class="d-block">
							<text>{{item.currency_name}}</text>
							<text>/{{item.legal_name}}</text>
						</text>
						<text class="d-block">24H: {{Number(item.volume) | setPrecision(item.precision_length)}}</text>
					</view>
					<view class="text-center m-content">
						<text class="d-block"
							:style="{color:$utils.getColor(item.change)}">{{Number(item.now_price)  | setPrecision(item.precision_length)}}</text>
						<text class="d-block" v-if="$store.state.fiat.currency_code != 'USD'">
							{{(item.now_price * $store.state.fiat.rate)  | setPrecision(item.precision_length) }} {{$store.state.fiat.currency_code}}
						</text>
					</view>
					<view class="d-flex justify-content-end m-right">
						<view :style="{backgroundColor:$utils.getColor(item.change)}">
							{{item.change + '%'}}
						</view>
					</view>
				</navigator>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				currentNav: 1,
				
				quotation: [],
				quotationOriginal: [],
				socket: null,
				optionalList:[],//添加自选
			};
		},
		onLoad() {
			const _this = this
			uni.setNavigationBarTitle({
				title:_this.$t("home.market")
			})
		},
		onShow() {
			uni.showTabBar()
			this.getQuotation()
			this.startSocket()
		},
		methods: {
			navChangeFunc(val) {
				this.currentNav = val
			},
			// 获取行情
			async getQuotation() {
				const ret = await this.$u.api.market.getOptionalList()
				const optionalList = ret.message
				this.$u.api.index.getQuotation().then(res => {
					this.quotationOriginal = res.message[0].quotation.map(item=>{
						item.precision_length = this.$utils.getPrecisionLength(item.now_price)
						return item
					})
					
					if(optionalList.length){
						let optional = []
						optionalList.forEach(item => {
							const has = this.quotationOriginal.find(el => item.currency_id == el.currency_id)
							if(has) optional.push(has)
						})
						this.optionalList = optional
					}else{
						this.currentNav = 1
						this.optionalList = []
					}
					
					//对quotation进行排序检测
					this.quotation = this.$u.deepClone(this.quotationOriginal)
					uni.setStorageSync('quotation',this.quotation)
				})
			},
			sort(index = 0) {
				const {quotationNav} = this
				let navItem = quotationNav[index]
				let sort
				if(navItem.sort == 'none'){
					sort = 'up'
					navItem.sort  = 'up'
				}else if(navItem.sort == 'up'){
					sort = 'down'
					navItem.sort  = 'down'
				}else{
					sort = 'none'
					navItem.sort  = 'none'
				}
				this.quotationNav.splice(index,1,navItem)

				let sortMethod = null
				if (sort == 'none') {
					this.quotation = this.quotationOriginal
				}else{
					if (index == 0) {
						//对交易对做排序
						if (sort == 'up') {
							sortMethod = (a, b) => (a.currency_name + '').localeCompare(b.currency_name + '')
						} else {
							sortMethod = (a, b) => (b.currency_name + '').localeCompare(a.currency_name + '')
						}
					} else if (index == 1) {
						if (sort == 'up') {
							sortMethod = (a, b) => Number(a.now_price) - Number(b.now_price)
						} else {
							sortMethod = (a, b) => Number(b.now_price) - Number(a.now_price)
						}
					} else if (index == 2) {
						if (sort == 'up') {
							sortMethod = (a, b) => Number(a.change) - Number(b.change)
						} else {
							sortMethod = (a, b) => Number(b.change) - Number(a.change)
						}
					}
					this.quotation = this.quotation.sort(sortMethod)
				}
			},
			async startSocket() {
				const _this = this
				let ret = await this.$u.api.setting.getUserInfo()
				ret = ret.message
				this.$store.state.socket.emit('login', ret.id);
				this.$store.state.socket.on('daymarket', res => {
					const has = _this.quotation.findIndex(item => item.currency_id == res.currency_id)
					if(has > -1){
						const item = {
							..._this.quotation[has],
							...res
						}
						_this.quotation.splice(has, 1, item)
					}
					
					const has2 = _this.optionalList.findIndex(item => item.currency_id == res.currency_id)
					if(has2 > -1){
						const item2 = {
							..._this.optionalList[has2],
							...res
						}
						_this.optionalList.splice(has2, 1, item2)
					}
					
				});
			},

		},
		computed: {
			i18n() {
				return this.$t("transaction")
			},
			navs: [],
			navs(){
				const i18n = this.$t("transaction")
				return [{
					name: i18n.favorites
				}, {
					name: "USDT"
				}]
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
		onHide(){
			this.$store.state.socket.removeListener('daymarket')
		},
		onUnload() {
			this.$store.state.socket.removeListener('daymarket')
		}

	}
</script>

<style lang="scss">
	.wrapper{
		position: relative;
		z-index: 10;
		&::after{
			content: "";
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom:0;
			background-image: url('@/static/image/icon/market-bg.png');
			background-size: contain;
			background-position:center 30vh;
			background-repeat: no-repeat;
			background-attachment: fixed;
			z-index: -1;
		}
	}
	.market {
		.nas-title {
		    font-size: 15px;
		    font-family: Roboto;
		    font-weight: 500;
		    color: #999;
		}
		.market-item {
			overflow: hidden;
			padding: 32rpx 0;
			border-bottom: 1px solid rgba(255,255,255,.1);
			align-items: center;
			display: grid;
			grid-template-columns:1.1fr 1fr 1fr;

			.right {
				margin-left: auto;
				width: 150rpx;
				height: 60rpx;
				line-height:60rpx;
				text-align: center;
				border-radius: 10rpx;
				background-color: #15be97;
				color: #fff;
				font-size: 30rpx;
			}
			.m-left{
				font-size: 22rpx;
				font-family: Roboto;
				font-weight: 500;
				color: #333;
			}
			.m-content{
				font-size: 30rpx;
				font-family: Roboto;
				font-weight: 500;
			}
			.m-right > view{
				width: 116rpx;
				height: 50rpx;
				text-align: center;
				line-height: 50rpx;
				color: #fff;
				border-radius: 8rpx;
			}
		}
	}
</style>
