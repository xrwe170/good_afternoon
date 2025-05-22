<template>
	<view class="home">
		<view class="fin-title-box">
			<text>{{i18n.lockming}}</text> 
		</view>
		<view class="fin-menu-box">
			<view class="d-flex align-items-center justify-content-between ">
				<view class="d-flex align-items-center fin-menu" v-for="item in menu.slice(0,2)" @click="homeNavJump(item.url,item.open_type)">
					<image :src="`../../static/image/icon/${item.icon}.png`" style="width: 69rpx;height: 62rpx; margin-left: 34rpx;" mode=""></image>
					<text class="fin-menu-name">{{item.name}}</text>
				</view>
			</view>
			<view class="d-flex align-items-center justify-content-between " v-for="item in menu.slice(2,3)" @click="homeNavJump(item.url,item.open_type)">
				<image :src="`../../static/image/icon/${item.icon}.png`" style="width: 69rpx;height: 62rpx; margin-left: 34rpx;" mode=""></image>
				<text class="fin-menu-name">{{item.name}}</text>
			</view>
		</view>
		
		<!-- 投资理财 -->
		<view class="mb-50" v-if="invest.length">
			<view class=" px-30">
				<view class="d-flex-between-center text-warning">
					<text class="d-block font-size-32 font-weight-bold">{{i18n.invest}}</text>
					<view class="more"><u-icon name="arrow-right" size="28" @click="$utils.jump('/pages/invest/invest')"></u-icon></view>
				</view>
				<view class="d-flex align-items-center mt-20 font-size-32">
					<text class="mr-30 pb-20 nav-item text-warning" @click="activeInvest = 0" :class="activeInvest == 0 ? 'active' : 'opacity-75'">BTC {{i18n.section}}</text>
					<text class="mr-30 pb-20 nav-item text-warning" @click="activeInvest = 1" :class="activeInvest == 1 ? 'active' : 'opacity-75'">ETH {{i18n.section}}</text>
				</view>
			</view>
			<view class="mx-30 my-20 bg-secondary p-30 border-radius-20 bg-black" v-for="item in invest[activeInvest].slice(0,3)" @click="$utils.jump('/pages/invest/purchase?id='+item.id)">
				<view class="d-flex align-items-center">
					<view class="border-radius-50per d-flex align-items-center justify-content-center" style="width: 50rpx;height: 50rpx;" :style="{backgroundColor:$utils.getCurrencyColor(item.currency_name)}">
						<text class="iconfont font-size-40 text-white" :class="`icon-${item.currency_name}`" ></text>
					</view>
					<text class="font-weight-bold ml-12 font-size-32 text-warning">{{item.name}}</text>
				</view>
				<view class="d-grid-columns-3 mt-20">
					<view class="">
						<text class="d-block opacity-50 font-size-24 mb-10" style="color: #aaa;">{{i18n.apr}}</text>
						<text class="d-block font-size-40 font-weight-bold" :style="{color:$upColor}">{{item.rate}}%</text>
					</view>
					<view class="text-center">
						<text class="d-block opacity-50 font-size-24 mb-10" style="color: #aaa;">{{i18n.linkedReferencePrice}}</text>
						<text class="d-block font-size-40 font-weight-bold text-white opacity-80" >{{item.exercise_price}}</text>
					</view>
					<view class="text-right">
						<text class="d-block opacity-50 font-size-24 mb-10" style="color: #aaa;">{{i18n.holdingDays}}</text>
						<text class="d-block font-size-40 font-weight-bold text-white opacity-80">{{item.remainDays + i18n.day}}</text>
					</view>
				</view>
			</view>
		</view>
		
		<!-- 锁仓挖矿 -->
		<view class="fin-loc-box" v-if="lockming.length">
			<view class="d-flex-between-center">
				<text class="fin-loc-title">{{i18n.lockming}}</text>
				<!-- <view class="more"><u-icon name="arrow-right" size="28" @click="$utils.jump('/pages/lockming/welcome')"></u-icon></view> -->
				<image src="/static/image/img/gengduo.png" style="width: 50rpx; height: 50rpx;" @click="$utils.jump('/pages/lockming/welcome')"></image>
			</view>
			
			
			<view class="d-flex align-items-center fin-loc-li"  v-for="(item,index) in lockming"  @click="jump2Lockming(item)">
				<view class="d-flex align-items-center justify-content-center fin-loc-icon" style="background-color: rgb(46, 92, 209);">
					<text>$</text>
				</view>
				<view class="flex1">
					<view class="d-flex align-items-center justify-content-between">
						<view class="fin-loc-li-name">
							{{item.currency_name + ' ' + i18n.mining}}
						</view>
						<view class="fin-loc-li-day">
							{{item.day + i18n.day}}
						</view>
					</view>
					<view class="d-flex align-items-center justify-content-between">
						<view class="fin-loc-li-min">{{Number(item.save_min) + ' ' + i18n.minimum}}</view>
						<view class="fin-loc-li-rate">{{i18n.dailyReturnRate}}</view>
						<view class="fin-loc-li-interest">{{item.intro?item.intro:Number(item.interest_rate)+'%'}}</view>
					</view>
				</view>
			</view>
				
			
		 	<!-- <view class="m-30 mt-20 high-quality-project">
		 		<swiper style="height: 390rpx;" :acceleration="true" :display-multiple-items="2" :indicator-dots="false"
		 			:autoplay="false" :circular="false">
		 			<swiper-item v-for="(item,index) in lockming.slice(0,5)">
		 				<view class="high-quality-project-item bg-black text-white" @click="jump2Lockming(item)">
		 					<text class="hige-quality-project-sskc">{{item.day + i18n.day}}</text>
		 					<view class="mx-auto border-radius-50per d-flex align-items-center justify-content-center" style="width: 80rpx;height: 80rpx;" :style="{backgroundColor:$utils.getCurrencyColor(item.currency_name)}">
		 						<text class="iconfont font-size-48 text-white" :class="`icon-${item.currency_name}`" ></text>
		 					</view>
							
		 					<text class="d-block font-size-32 mt-20">{{item.currency_name + ' ' +i18n.mining}}</text>
		 					<text
		 						class="d-block font-size-28 opacity-50 mt-4">{{Number(item.save_min) + ' ' + i18n.minimum}}</text>
		 					<text
		 						class="d-block font-size-40 text-error mt-30 font-weight-bold">{{item.intro?item.intro:Number(item.interest_rate)+'%'}}</text>
		 					<text class="d-block font-size-28 mt-2">{{i18n.dailyReturnRate}}</text>
		 				</view>
		 			</swiper-item>
		 		</swiper>
		 	</view> -->
		 </view>
		
		<!-- ieo -->
		<view class="mb-50" v-if="ieo.length">
			<view class=" px-30 d-flex-between-center text-warning">
				<text class="d-block font-size-32 font-weight-bold">{{i18n.ieo}}</text>
				<view class="more"><u-icon name="arrow-right" size="28" @click="$utils.jump('/pages/ieo/ieo')"></u-icon></view>
			</view>
			<view class="mx-30 my-20 p-30 box-shadow border-radius-20 mb-20 bg-black text-white" v-for="item in ieo.slice(0,3)" >
				<view class="d-flex-between-center">
					<text class="d-block font-size-30 text-warning font-weight-bold">{{item.title}}</text>
					<u-icon name="arrow-rightward" size="40" v-if="item.time_status == 2" :color="$baseColor" @click="$utils.jump('/pages/ieo/subscribe?project_id=' + item.id)"></u-icon>
				</view>
				<view class="d-flex-between-center mt-20">
					<view class="d-flex align-items-baseline">
						<text class="opacity-50 font-size-24">{{i18n.lockPeriod}}:</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{item.day + i18n.day}}</text>
					</view>
					<text class="text-success font-size-24" v-if="item.time_status == 2">{{i18n.ing}}</text>
					<text class="text-error font-size-24" v-else-if="item.time_status == 3">{{i18n.done}}</text>
				</view>
				<view class="d-grid-columns-2 mt-20">
					<view class="d-flex align-items-baseline">
						<text class="opacity-50 font-size-24">{{i18n.subscribed}}({{item.currency_name}}):</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{parseFloat(item.total_sell)}}</text>
					</view>
					<view class="d-flex align-items-baseline">
						<text class="opacity-50 font-size-24">{{i18n.total}}({{item.currency_name}}):</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{parseFloat(item.amount)}}</text>
					</view>
				</view>
				<view class="d-grid align-items-baseline" style="grid-template-columns:2fr 1fr">
					<u-line-progress :percent="Number(item.percentage)" inactive-color="#666" :show-percent="false" class="mt-20" v-if="item.percentage > 0"></u-line-progress>
					<u-line-progress :percent="0" class="mt-20" inactive-color="#666" :show-percent="false" v-else></u-line-progress>
					<view class="d-flex align-items-baseline justify-content-end">
						<text class="opacity-50 font-size-24">{{i18n.remaining}}:</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{parseFloat(item.percentage)}}%</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				menu: [],
				ieo:[],
				lockming:[],
				invest:[],
				activeInvest:0
			};
		},
		onLoad() {
			//获取首页菜单
			this.getMenu()
			
			const _this = this
			uni.setNavigationBarTitle({
				title:_this.$t("home.financial")
			})
		},
		methods: {
			//获取首页菜单
			getMenu() {
				const {
					i18n
				} = this
				this.$u.api.index.getMenu().then(res => {
					this.menu = res.message.filter(item => item.is_financial).map(item=>{
						item.name = i18n[item.title]
						return item
					})
					
					this.menu.forEach(item => {
						if(item.title == 'ieo'){
							this.getIEOProject()
						}else if(item.title == 'lockming'){
							this.getLockming()
						}else if(item.title == 'invest'){
							this.getInvest()
						}
					})
				})
			},
			// 获取ieo认购
			getIEOProject(){
				this.$u.api.ieo.getIEOProject(1,10).then(res=>{
					this.ieo = res.message.list
				})
			},
			// 获取锁仓挖矿
			getLockming(){
				this.$u.api.lockming.getLockming().then(res=>{
					this.lockming = res.message
				})
			},
			// 获取投资理财
			async getInvest(){
				let list_BTC = await this.$u.api.invest.getList(1)
				let list_ETH = await this.$u.api.invest.getList(2)
				
				list_BTC = list_BTC.message.dual_currencys.map(item => {
					item.remainDays = this.datedifference(item.end_time,item.start_time)
					return item
				})
				
				list_ETH = list_ETH.message.dual_currencys.map(item => {
					item.remainDays = this.datedifference(item.end_time,item.start_time)
					return item
				})
				
				this.invest = [list_BTC, list_ETH]
				
			},
			datedifference(sDate1, sDate2) {    //sDate1和sDate2是2006-12-18格式
				var dateSpan,
					tempDate,
					iDays;
				sDate1 = Date.parse(sDate1);
				sDate2 = Date.parse(sDate2);
				dateSpan = sDate2 - sDate1;
				dateSpan = Math.abs(dateSpan);
				iDays = Math.floor(dateSpan / (24 * 3600 * 1000));
				return iDays
			},
			//跳转至锁仓挖矿详情
			jump2Lockming(item) {
				uni.setStorageSync('lockming', item)
				uni.navigateTo({
					url: '/pages/lockming/welcome'
				})
			},
			//点击首页菜单的跳转
			homeNavJump(url,openType){
				if(url){
					this.$utils.jump(url,openType)
				}else{
					this.$utils.showToast(this.$t("common.functionLoading"))
				}
			}
		},
		computed: {
			i18n() {
				return this.$t("financial")
			}
		}
	}
</script>

<style lang="scss" scoped>
	.home {
		overflow: hidden;
		padding-top: var(--status-bar-height);
		position: relative;
		min-height: 100vh;
		z-index: 10;
		&::after {
			content: "";
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom:0;
			background-image: url('@/static/image/icon/login-bg.png');
			background-size: contain;
			background-position: 100% top;
			background-repeat: no-repeat;
			z-index: -1;
		}
		.fin-title-box{ 
			padding: 40rpx 0 0 34rpx;
			&> uni-text{
				font-size: 38rpx;
				font-family: Roboto;
				font-weight: 500;
				color: #333;
			}
		}
		.fin-menu-box{
			margin: 44rpx 0 39rpx 34rpx;
			.fin-menu {
			    width: 245rpx;
			    height: 96rpx;
			    background: #fff;
			    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
			    border-radius: 32rpx;
				
				.fin-menu-name{
					font-size: 24rpx;
					font-family: Roboto;
					font-weight: 700;
					color: #333;
					margin-left: 30rpx;
				}
			}
		}
		
		.fin-loc-box{
			padding: 39rpx 34rpx 0 34rpx;
			.fin-loc-title {
			    font-size: 38rpx;
			    font-family: Roboto;
			    font-weight: 500;
			    color: #333;
			}
			.fin-loc-li{
			    margin-top: 42rpx;
				.fin-loc-icon {
				    width: 76rpx;
				    height: 76rpx;
				    border-radius: 100rpx;
				    font-size: 37rpx;
				    font-family: Roboto;
				    font-weight: 400;
				    color: #fff;
				    margin-right: 32rpx;
				}
				.flex1{
					flex: 1;
					 .fin-loc-li-name {
					    font-size: 30rpx;
					    font-family: Roboto;
					    font-weight: 400;
					    color: #333;
					}
					.fin-loc-li-day {
					    font-size: 30rpx;
					    font-family: Roboto;
					    font-weight: 400;
					    color: #2bc016;
					}
					.fin-loc-li-min {
					    font-size: 30rpx;
					    font-family: Roboto;
					    font-weight: 500;
					    color: #333;
					}
					.fin-loc-li-rate {
					    font-size: 24rpx;
					    font-family: Roboto;
					    font-weight: 500;
					    color: #333;
					}
					.fin-loc-li-interest {
					    font-size: 20rpx;
					    font-family: Roboto;
					    font-weight: 700;
					    color: red;
					}
				}
			}
		}
	}
	
	.nav-item{
		position: relative;
		&.active::after{
			display: block;
			position: absolute;
			content: "";
			left: 30%;
			right: 30%;
			height: 4rpx;
			background-color: $uni-color-warning;
			bottom: -2rpx;
		}
	}
	
	.high-quality-project {
		.high-quality-project-item {
			border-radius: 20rpx;
			width: 96%;
			text-align: center;
			padding-top: 40rpx;
			height: 390rpx;
	
			.hige-quality-project-sskc {
				background-image: linear-gradient(to right, #00d789, #00e1cc);
				font-size: 24rpx;
				color: #fff;
				height: 40rpx;
				line-height: 40rpx;
				padding: 0 18rpx;
				border-radius: 10rpx 0 10rpx 0;
				display: block;
				position: absolute;
				left: 0;
				top: 0;
			}
		}
	}
	
	.more{
		width: 40rpx;
		height: 40rpx;
		border-radius: 50%;
		background-color: #222;
		display: flex;
		align-items: center;
		justify-content: center;
	}
</style>
