<template>
	<view class="text-white">
		<u-navbar :title="i18n.invest">
			<text class="iconfont icon-yonghu font-size-38 font-weight-bold" slot="right" @click="$utils.jump('/pages/invest/mine')"></text>
		</u-navbar>
		
		<view class="m-30">
			<!-- 一级菜单 -->
			<view class="d-flex nav">
				<text class="item font-size-32 nav-1" :class="{active:currentNav == 0}" @click="setNav(0)">{{'BTC' + i18n.section}}</text>
				<text class="item font-size-32 ml-10 nav-2" :class="{active:currentNav == 1}" @click="setNav(1)">{{'ETH' + i18n.section}}</text>
				<text class="item nav-back" :style="navBackStyle"></text>
			</view>
			<!-- 二级菜单 -->
			<view class="mt-24 d-flex sort-nav">
				<text class="item mr-12" v-for="(item,index) in sortNav" :class="{active:currentSortNav == index}" @click="sortNavChange(index)" :key="item">{{item}}</text>
			</view>
			
			<view class="mt-30">
				<view class="box-shadow border-radius-20 mb-20 bg-black" v-for="item in invest">
					<text class="px-20 py-20 d-block u-border-bottom font-size-32">{{item.name}}</text>
					<view class="p-20">
						<view class="py-10">
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.apr}}:</text>
								<text class="font-size-32 font-weight-bold ml-10" :style="{color:$downColor}">{{item.rate}}%</text>
							</view>
						</view>
						<view class="d-grid-columns-2  mt-8">
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.linkedReferencePrice}}:</text>
								<text class="ml-10">{{item.exercise_price}}</text>
							</view>
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.remainingShares}}:</text>
								<text class="ml-10">{{item.remaining_number + i18n.share}}</text>
							</view>
						</view>
						<view class="d-grid-columns-2 mt-8">
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.holdingDays}}:</text>
								<text class="ml-10">{{item.holdingDays + i18n.day}}</text>
							</view>
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.maturityDate}}:</text>
								<text class="ml-10" :class="{'text-error' : item.buyStatus == 0}">{{item.end_time}}</text>
							</view>
						</view>
						<view class="d-flex-between-center mt-20">
							<u-line-progress :active-color="$downColor" :percent="item.progress"></u-line-progress>
						</view>
						<button class="warning-button mt-20 py-0 font-size-28" v-if="item.buyStatus" @click="$utils.jump('/pages/invest/purchase?id='+item.id)">{{i18n.purchase}}</button>
					</view>
				</view>
				<default-page v-if="!invest.length"></default-page>
			</view>
		</view>
		
		<!-- 悬浮的帮助按钮 -->
		<view class="footer box-shadow border-radius-50per text-center" @click="$utils.jump('/pages/invest/help')">
			<u-icon name="question-circle" size="36"  :color="$downColor" ></u-icon>
			<text class="d-block font-size-22">{{i18n.help}}</text>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				currentNav:0,
				navBackStyle:{left:0,width:'20rpx'},
				currentSortNav:0,
				invest:[]
			};
		},
		onLoad() {
			setTimeout(()=>{
				this.setNav(0)
			},100)
		},
		onShow() {
			this.getList()
		},
		methods:{
			getList(){
				const currency_id = this.currentNav == 0 ? 1 : 2
				const type = this.currentSortNav == 0 ? 'call' : 'put'
				this.$u.api.invest.getList(currency_id,type).then(res=>{
					let list = res.message.dual_currencys || []
					this.invest = list.map(item => {
						item.progress = Number((Number(item.purchased_number) / Number(item.total_number) * 100).toFixed(2))
						item.holdingDays = this.datedifference(item.end_time,item.start_time)
						const timestamp = Date.parse(new Date());
						item.status = Number(item.status)
						//剩余天数
						item.remainDays = this.datedifference(item.end_time,this.$u.timeFormat(timestamp,'yyyy-mm-dd'))
						item.buyStatus = 1
						if(item.remainDays <= 0 || !item.status) item.buyStatus = 0
						
						return item
					})
				})
			},
			setNav(index){
				this.$u.throttle(()=>{
					this.currentNav = index
					let view = uni.createSelectorQuery().in(this);
					view.select('.nav').boundingClientRect();
					view.select('.nav-' + 1).boundingClientRect();
					view.select('.nav-' + 2).boundingClientRect();
					view.exec(res => {
						const left = res[index + 1].left - res[0].left + 'px'
						const width = res[index + 1].width + 'px'
						this.navBackStyle = {
							left,
							width
						}
					});
					this.getList()
				},200)
			},
			sortNavChange(index){
				this.currentSortNav = index;
				this.getList()
			},
			datedifference(sDate2, sDate1) {    //sDate1和sDate2是2006-12-18格式  
				var dateSpan,
					tempDate,
					iDays;
				sDate1 = Date.parse(sDate1);
				sDate2 = Date.parse(sDate2);
				dateSpan = sDate2 - sDate1;
				console.log(dateSpan);
				//dateSpan = Math.abs(dateSpan);
				iDays = Math.floor(dateSpan / (24 * 3600 * 1000));
				return iDays
			}
		},
		computed:{
			i18n(){
				return this.$t("invest")
			},
			sortNav(){
				const i18n = this.$t("invest")
				return ['BTC' + i18n.invest,'USDT' +i18n.invest]
			},
		}
	}
</script>

<style lang="scss">
	.navbar {
		height: 88rpx;
		line-height: 88rpx;
		color: rgb(96, 98, 102);
	}
	
	.nav{
		height: 68rpx;
		line-height: 68rpx;
		position: relative;
		.item{
			height: inherit;
			padding: 0 40rpx;
			transition: all .3s ease 0s;
			border-radius: 40rpx;
			&.active{
				color: #fff;
				background-color: $uni-color-warning;
			}
		}
		.nav-back{
			position: absolute;
			top: 0;
			bottom: 0;
			background-color: $uni-color-warning;
			z-index: -1;
		}
	}
	
	.sort-nav{
		.item{
			color: rgba(51,51,51,.5);
			background-color: #eff2fb;
			border-radius: 8rpx;
			padding: 6rpx 24rpx;
			border:2rpx solid #eff2fb;
			transition: all .3s ease 0s;
			&.active{
				border-color: $uni-color-warning;
				color: $uni-color-warning;
				background-color: #fff;
			}
		}
	}
	
	.footer{
		position: fixed;
		right: 30rpx;
		bottom: 10vh;
		width: 88rpx;
		padding: 10rpx 0;
		color: $uni-color-warning;
		background-color: #ffffff;
	}
</style>
