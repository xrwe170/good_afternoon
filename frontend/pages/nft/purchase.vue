<template>
	<view class="mx-30 pb-200">
		<view class="status_bar"></view>
		<view class="my-30 text-center animated fadeInDown" style="height: 88rpx;line-height: 88rpx;">
			<view class="white-circle box-shadow position-absolute" @click="$utils.jump(1,'navigateBack')">
				<u-icon name="arrow-leftward" size="38" ></u-icon>
			</view>
			<text class="font-size-36 text-deepblue font-weight-bold">{{i18n.placeABid}}</text>
		</view>
		<view class="mt-50 d-flex-between-center">
			<view class="position-relative overflow-hidden box-shadow border-radius-20 animated fadeInLeft" style="width: 200rpx;height: 200rpx;">
				<u-image :src="data.image | retImageUrl" width="100%" height="100%"></u-image>
				<view class="position-absolute animated fadeInRight" style="width: 30rpx;height: 40rpx;left: 20rpx;top: 20rpx;">
					<image :src="`../../static/image/icon/rarity-${data.rarity_name}.png`" class="w-100 h-100"></image>
				</view>
			</view>
			<view class="ml-24 animated fadeInRight" style="flex: 1;">
				<text class="d-block text-deepblue font-weight-bold font-size-36">{{data.name}}</text>
				<text class="d-block mt-16 opacity-50 line-2">{{i18n.rarity + ': ' + data.rarity}}</text>
				<text class="d-block mt-16 opacity-50 line-2" v-if="data.description">{{data.description.substring(0,100)}}</text>
				<text class="d-block text-error font-weight-bold mt-20">{{i18n.createdBy}} {{data.author_name|| i18n.nonickname}}</text>
			</view>
		</view>
		
		<!-- 出价框 -->
		<view class="border mt-50 py-30 px-50 border-radius-20 d-flex align-items-center justify-content-between animated fadeInUp" style="border-color: rgba(0,0,0,.05);">
			<view class="jiajian" @click="calculate(0)">
				<u-icon name="minus" size="36" color="#666"></u-icon>
			</view>
			<view class="d-flex align-items-center">
				<view class="white-circle box-shadow" style="width: 70rpx;height: 70rpx;">
					<text class="iconfont font-size-42 opacity-75 text-primary" :class="'icon-' + data.currency_name"></text>
				</view>
				<text class="font-size-38 text-deepblue font-weight-bold ml-20">{{Number(price.toFixed(4)) + ' ' + data.currency_name}}</text>
			</view>
			<view class="jiajian" @click="calculate(1)">
				<u-icon name="plus" size="36" color="#666"></u-icon>
			</view>
		</view>
		<view class="d-block text-center opacity-50 mt-20 align-items-center font-size-22  animated fadeInUp">
			<text class="d-block">{{i18n.place_alert2}} <text class="font-weight-bold font-size-28 mx-8">{{Number((+data.price).toFixed(4))}}</text> {{data.currency_name}}</text>
			<text class="d-block">{{i18n.per_increase }} <text class="font-weight-bold font-size-28 mx-8">{{Number(data.per_increase)}}</text> {{ data.currency_name}}</text>
		</view>
		
		<!-- 距离结束的倒计时 -->
		<view class="mt-50 animated fadeInUp" v-if="data.remainTime && data.remainTime > 0" >
			<block v-if="$utils.stamp2Time(data.remainTime,'d') == '00'">
				<view class="d-flex justify-content-center">
					<view class="timer" v-if="$utils.stamp2Time(data.remainTime,'h')">
						{{$utils.stamp2Time(data.remainTime,'h')}}
					</view>
					<text>:</text>
					<view class="timer" v-if="$utils.stamp2Time(data.remainTime,'m')">
						{{$utils.stamp2Time(data.remainTime,'m')}}
					</view>
					<text>:</text>
					<view class="timer" v-if="$utils.stamp2Time(data.remainTime,'s')">
						{{$utils.stamp2Time(data.remainTime,'s')}}
					</view>
				</view>
				<text class="d-block text-center font-size-22 opacity-50 mt-10">{{i18n.cjdjs}}</text>
			</block>
			<block v-else >
				<view class="timer-wrap">
					{{data.end_time | date('yyyy-mm-dd hh:MM:ss')}}
				</view>
				<text class="d-block text-center font-size-22 opacity-50 mt-10">{{i18n.cjjzsj}}</text>
			</block>
			
		</view>
		
		<!-- 出价列表 -->
		<view class="mx-30 mt-50 animated fadeInUp">
			<text class="d-block opacity-50 font-weight-bold">{{i18n.CURRENT_BID}}</text>
			<view class="mt-24  border-radius-10 d-flex-between-center p-30" v-for="item in order.slice(0,3)" style="background-image: linear-gradient(225deg, #edf1f9 0%, #e4efe9 100%);">
				<view class="d-flex align-items-center">
					<u-image :src="item.head_portrait  | retImageUrl" width="70" height="70" border-radius="50%" ></u-image>
					<view class="ml-20">
						<text class="d-block font-weight-bold text-deepblue">{{i18n.bidPlacedBy}}: <text class="text-error ml-8">{{item.nick_name}}</text></text>
						<text class="d-block opacity-50 font-size-24 mt-4">{{item.created}}</text>
					</view>
				</view>
				<view class="font-weight-bold font-size-32 text-deepblue">
					{{item.price + ' ' + item.currency_name}}
				</view>
			</view>
			<default-page v-if="!order.length"></default-page>
		</view>
		
		<!-- note -->
		<view class="border mt-50 py-30 px-50 border-radius-20 d-flex animated fadeInUp" style="border-color: rgba(0,0,0,.05);align-items: flex-start;">
			<u-icon name="info-circle-fill" color="#ff9900" size="40" class="position-relative" style="top: 6rpx;"></u-icon>
			<view class="ml-20">
				<text class="d-block text-deepblue font-size-32 font-weight-bold" style="flex: 1;">{{i18n.note}}</text>
				<view class="font-size-24 opacity-75">
					{{i18n.place_alert3}}<text class="text-error font-weight-bold font-size-32 mx-10">{{Number(data.margin) + 'USDT'}}</text>{{i18n.place_alert4}}
				</view>
				<text  class="font-size-24 opacity-75 d-block mt-20">{{i18n.place_alert6}}</text>
			</view>
		</view>
		
		<!-- 确认出价 -->
		<view class="fixed-nav animated fedeInUp" v-if="data.buy_status == 1" @click="placeABid">
			{{i18n.confirmBid}}
		</view>
		<view class="fixed-nav fixed-nav-3 animated fedeInUp"  v-else>
			{{i18n.place_alert}}
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				data:{},
				order:[],
				price:0,
				interval:null,
				getOrderInterval:null,
			};
		},
		onLoad(options) {
			const {id} = options
			if(!id){
				this.$utils.showToast(this.$t("common.paramsWrong"));
				setTimeout(()=>{
					uni.navigateBack({
						delta:1
					})
				},1200)
				return
			}
			this.id = id
		},
		onShow() {
			this.getArtworkDetail()
		},
		methods:{
			getArtworkDetail(){
				this.$u.api.nft.getArtworkDetail(this.id).then(res=>{
					let data = res.message
					//初始竞拍价格，后期要改
					let limit = 0
					if(data.per_increase.toString().split('.')[1]){
						limit = data.per_increase.toString().split('.')[1].length
					}
					
					this.price = Number(((+data.price) + (+data.per_increase)).toFixed(limit))

					
					//看是否在购买时间内
					data.buy_status = data.status
					data.start_time = Date.parse(data.start_time)
					data.end_time = Date.parse(data.end_time) 
					const rarity = {'N':1,'R':2,'SR':3}
					data.rarity_name= data.rarity ? rarity[data.rarity] : 1
					const nowTimestamp = Date.parse(new Date()) 

					
					data.buy_status = this.$utils.nftBuyStatus(data.status,data.start_time,data.end_time)
					if(data.buy_status == 1){
						data.remainTime = (data.end_time - nowTimestamp) / 1000
					}

					this.data = data
					if(this.data.buy_status == 1){
						this.interval = setInterval(()=>{
							this.data.remainTime-- 
							if(this.data.remainTime == 0){
								data.buy_status = 0
								clearInterval(this.interval)
							}
						},1000)
					}else{
						this.$utils.showToast(this.i18n.place_alert)
						setTimeout(()=>{
							uni.redirectTo({
								url:'/pages/nft/nft_new'
							})
						},1200)
					}
					
					
					//获取出价记录
					this.getPurchaseOrder()
					this.getOrderInterval = setInterval(()=>{
						this.getPurchaseOrder()
					},2000)
				})
			},
			//加减价格
			calculate(dirction){
				let {price} = this
				let {per_increase,price:originalPrice,currency_name} = this.data
				originalPrice = Number(originalPrice)
				//per_increase是几位小数
				let limit = 0
				if(per_increase.toString().split('.')[1]){
					limit = per_increase.toString().split('.')[1].length
				}
				if(dirction == 0){
					//如果是减
					if(price == originalPrice){
						this.$utils.showToast('出价需高于' + originalPrice + currency_name)
					}else if(price - originalPrice < per_increase){
						price = originalPrice
					}else{
						price = this.$utils.math(price,'-',per_increase) 
					}
				}else{
					price = this.$utils.math(price,'+',per_increase) 
				}
				this.price = Number(price.toFixed(limit))
			},
			placeABid(){
				this.$u.throttle(async ()=>{
					const {code,price:originPrice,per_increase} = this.data
					const {price,i18n} = this
					
					let limit = 0
					if(per_increase.toString().split('.')[1]) limit = per_increase.toString().split('.')[1].length
					const jiajia = Number((price - originPrice).toFixed(limit))
					if(jiajia <= 0) return this.$utils.showToast(i18n.wrongPrice)
					
					const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmAuction)
					if(!ret) return
					
					this.$u.api.nft.placeABid(code,jiajia).then(res=>{
						this.$utils.showToast(this.$t("common.success"))
						this.getPurchaseOrder()
						this.getArtworkDetail()
					})
				},500)
				
			},
			//获取出价记录
			getPurchaseOrder(){
				const code = this.data.code
				this.$u.api.nft.getPurchaseOrder(code).then(res=>{
					let list = res.message
					list.forEach(item => {
						item.price = Number((+item.price).toFixed(4))
					})
					this.order = list
				})
			}
		},
		computed:{
			i18n(){
				return this.$t("nft")
			}
		},
		onUnload() {
			if(this.interval){
				clearInterval(this.interval)
				this.interval = null
			}
			if(this.getOrderInterval){
				clearInterval(this.getOrderInterval)
				this.getOrderInterval = null
			}
			
		},
	}
</script>
<style>
	page,html,body{
		background-color: white;
	}
</style>
<style lang="scss" scoped>
.white-circle{
	width: 80rpx;height: 80rpx;
	border-radius: 50%;
	background-color: white;
	display: flex;
	align-items: center;
	justify-content: center;
}
.jiajian{
	width: 80rpx;
	height: 80rpx;
	border-radius: 10rpx;
	background-color: #F5F7FB;
	display: flex;
	align-items: center;
	justify-content: center;
}
.fixed-nav{
	position: fixed;
	bottom: 10vw;
	@extend .box-shadow;
	height: 100rpx;
	border-radius: 50rpx;
	transition: all .8s ease 0s;
	z-index: 999;
	background-color: #fff;
	left: 20vw;
	right: 20vw;
	display: flex;
	justify-content: center;
	align-items: center;
	font-weight: bold;
	font-size:36rpx;
	background-color: #000000;
	color: #fff;
	&.fixed-nav-3{
		background: none;
		background-color: #E4E4E1;
		background-image: radial-gradient(at top center, rgba(255,255,255,0.03) 0%, rgba(0,0,0,0.03) 100%), linear-gradient(to top, rgba(255,255,255,0.1) 0%, rgba(143,152,157,0.60) 100%);
		background-blend-mode: normal, multiply;
		border: none;
		font-size: 28rpx;
	}
}

.timer{
	width: 60rpx;
	height: 60rpx;
	line-height: 60rpx;
	text-align: center;
	border-radius: 16rpx;
	background: none;
	font-weight: bold;
	margin: 0 20rpx;
	color: $uni-color-deepblue;
	background-image: linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%);
}

.timer-wrap{
	background-image: linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%);
	font-size: 32rpx;
	width: 60vw;
	margin: 0 auto;
	padding: 30rpx 0;
	border-radius: 40rpx;
	font-weight: bold;
	text-align: center;
	color: $uni-color-deepblue;
	box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}

</style>
