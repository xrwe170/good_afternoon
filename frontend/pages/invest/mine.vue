<template>
	<view class="text-white">
		<view class="status_bar"></view>
		<u-navbar :title="i18n.myPosition"></u-navbar>
		<view class="m-30">
			<!-- 一级菜单 -->
			<view class="d-flex nav">
				<text class="item font-size-32 nav-1" :class="{active:currentNav == 0}" @click="setNav(0)">{{'BTC' + i18n.section}}</text>
				<text class="item font-size-32 ml-10 nav-2" :class="{active:currentNav == 1}" @click="setNav(1)">{{'ETH' + i18n.section}}</text>
				<text class="item nav-back" :style="navBackStyle"></text>
			</view>
			<!-- 二级菜单 -->
			<view class="mt-24 d-flex-between-center">
				<view class="d-flex sort-nav">
					<text class="item mr-12" v-for="(item,index) in sortNav" :class="{active:currentSortNav == index}" @click="sortNavChange(index)" :key="item">{{item}}</text>
				</view>
				<view class="font-size-32 d-flex align-items-center" @click="showSelectStatusNav=true">
					<text>{{i18n.all}}</text>
					<u-icon name="arrow-down" class="ml-10"></u-icon>
				</view>
			</view>
			
			<!-- 二级菜单选择 -->
			<u-action-sheet :list="statusNav" v-model="showSelectStatusNav" @click="selectStatusNav"></u-action-sheet>

			<view class="mt-30">
				<view class="box-shadow border-radius-20 mb-20 bg-black" v-for="item in invest">
					<view class="d-flex-between-center px-20 py-20 u-border-bottom  font-size-32">
						<text>{{item.name}}</text>
						<text class="font-size-28" :style="{color:statusNav[item.status].color}">{{getStatusText(item)}}</text>
					</view>
					<view class="p-20">
						<view class="py-10">
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.apr}}:</text>
								<text class="font-size-32 font-weight-bold ml-10" :style="{color:$downColor}">{{item.order_rate}}%</text>
							</view>
						</view>
						<view class="d-grid-columns-2  mt-8">
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.linkedReferencePrice}}:</text>
								<text class="ml-10">{{item.exercise_price + 'USDT '}}</text>
							</view>
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.remainingShares}}:</text>
								<text class="ml-10">{{item.remaining_number + i18n.share}}</text>
							</view>
						</view>
						<view class="d-grid-columns-2 mt-8">
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.holdingDays}}:</text>
								<text class="ml-10">{{item.day > 0 ? item.day : 0 + i18n.day}}</text>
							</view>
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.maturityDate}}:</text>
								<text class="ml-10">{{item.end_time}}</text>
							</view>
						</view>
						<view class="d-grid-columns-2 mt-8">
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.buyShares}}:</text>
								<text class="ml-10">{{item.number + i18n.share}}</text>
							</view>
							<view class="d-flex align-items-baseline">
								<text class="opacity-30 font-size-24">{{i18n.estimatedIncome}}:</text>
								<text class="ml-10">{{item.expectedProfits}}</text>
							</view>
						</view>
					</view>
				</view>
				<default-page v-if="!invest.length"></default-page>
			</view>
			
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
				invest:[],
				showSelectStatusNav:false,
				page:1,
				canGet:true,
				status:1
			};
		},
		onLoad() {
			setTimeout(()=>{
				this.setNav(0)
			},100)
		},
		onShow() {
	
		},
		methods:{
			getOrder(){
				if(!this.canGet) return
				let currency = this.currentNav == 0 ? 1 : 2
				const type = this.currentSortNav == 0 ? 'call' : 'put'
				this.$u.api.invest.order({currency,page:this.page,type,status:this.status}).then(res=>{
					const list = res.message.list
					if(list.length){
						list.forEach(el=>{
							el.create_time = this.$u.timeFormat(el.create, 'yyyy-mm-dd hh:MM:ss')
							el.holdingDays = this.datedifference(el.create_time,el.end_time)
							el.expectedProfits = this.calcIncome(el)
						})
						this.invest = [...this.invest,...list]
						this.page++
					}else{
						this.canGet = false
					}
					
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
					
					this.page = 1
					this.canGet = true
					this.invest = []
					this.getOrder()
				},200)
			},
			// 0：失败，1：已买入，2：结算成功，3：结算成功
			getStatusText(item){
				const {i18n} = this
				if(item.status == 3){
					return '+' + item.success + 'USDT';
				}else{
					return this.statusNav[item.status].text;
				}
			},
			//选择statusnav
			selectStatusNav(index){
				console.log(index);
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
			//计算预期收益
			calcIncome(el){
				const rate = Number(el.order_rate) / 100 //年化收益率
				const exercise_price = Number(el.exercise_price)
				const {number:value,price:close,day:holdingDays} = el //挂钩参考价
				let expectedProfits = ''
				
				//如果购买的是BTC或者ETH
				if(el.type == 'call'){
					if( close <= exercise_price ){
						expectedProfits = value * ( 1 + rate / 365 * holdingDays ) 
					}else{
						expectedProfits = value * exercise_price *  ( 1 + rate / 365 * holdingDays ) 
					}
				}else if(el.type == 'put'){
					//如果购买的是USDT
					if( close <= exercise_price ) {
						expectedProfits = value / exercise_price * ( 1 + rate / 365 * holdingDays )
					}else{
						expectedProfits = value * ( 1 + rate / 365 * holdingDays ) 
					}
				}
				expectedProfits = Number(expectedProfits).toFixed(4)
				if(close <= exercise_price){
					return expectedProfits + el.currency_name
				}else{
					return expectedProfits + 'USDT'
				}
			},
			sortNavChange(index){
				console.log();
				this.currentSortNav = index;
				this.page = 1
				this.canGet = true
				this.invest = []
				this.getOrder()
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
			statusNav(){
				const i18n = this.$t("invest")
				return [{
					color:this.$downColor,
					text:i18n.fail
				},{
					color:'#eeeeee',
					text:i18n.purchased
				},{
					color:'#f0ad4e',
					text:i18n.settlementing
				},{
					color:this.$upColor,
					text:i18n.success
				},]
			}
		},
		onReachBottom() {
			this.getOrder()
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
</style>
