<template>
	<view class="mx-30 pb-200">
		<view class="status_bar"></view>
		<view class="my-30 text-center" style="height: 88rpx;line-height: 88rpx;">
			<view class="white-circle box-shadow position-absolute" @click="$utils.jump(1,'navigateBack')">
				<u-icon name="arrow-leftward" size="38" ></u-icon>
			</view>
		</view>
		
		<view class="position-relative animated fadeInDown">
			<view class="border-radius-50per box-shadow mx-auto" style="width: 300rpx;height: 300rpx;">
				<u-image :src="data.image | retImageUrl" width="300" height="300" border-radius="50%"></u-image>
			</view>
			<view class="position-absolute animated fadeInRight" style="width: 60rpx;height: 80rpx;right: 30rpx;top: -30rpx;">
				<image :src="`../../static/image/icon/rarity-${data.rarity}.png`" class="w-100 h-100"></image>
			</view>
			<view class="ml-24 text-center">
				<text class="d-block text-deepblue font-weight-bold font-size-50 text-center mt-30">{{data.name}}</text>
				<text class="d-block mt-16 mx-100  line-3 font-size-32 text-deepblue" v-if="data.description">{{data.description.substring(0,100)}}</text>
			</view>
		</view>
		
		
		
		<!-- 距离结束的倒计时 -->
		<view class="mt-60 animated fadeInUp" v-if="data.remainTime && data.remainTime > 0" >
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
				<text class="d-block text-center font-size-22 opacity-50 mt-20">{{i18n.cjjzsj}}</text>
			</block>
		</view>
		
		<view class="border mt-50 py-30 px-50 animated fadeInUp  d-flex align-items-center justify-content-between" style=" border-radius: 60rpx;border-color: rgba(0,0,0,.05);">
			<text class="font-weight-bold text-deepblue font-size-32">{{i18n.price}}</text>
			<view class="d-flex align-items-center">
				<view class="white-circle box-shadow" style="width: 70rpx;height: 70rpx;">
					<text class="iconfont font-size-42 opacity-75 text-primary" :class="'icon-' + data.currency_name"></text>
				</view>
				<text class="font-size-38 text-deepblue font-weight-bold ml-20">{{price + ' ' + data.currency_name}}</text>
			</view>
		</view>
			
		<!-- 确认出价 -->
		<view class="fixed-nav animated fadeInUp" v-if="data.buy_status == 1" @click="purchase">
			{{i18n.confirPurchase}}
		</view>
		<view class="fixed-nav fixed-nav-3 animated fadeInUp"  v-else>
			{{i18n.place_alert}}
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				data:{},
				price:0
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
					this.price = Number(data.price)
					const rarity = {'N':1,'R':2,'SR':3}
					data.rarity = data.rarity ? rarity[data.rarity] : 1
					//看是否在购买时间内
					data.buy_status = data.status
					data.start_time = Date.parse(data.start_time)
					data.end_time = Date.parse(data.end_time) 
					const nowTimestamp = Date.parse(new Date()) 
					
					// if(data.pay_type == 3) data.image = '/upload_nft/bind_box.png'
					
					if(data.status == 1 &&  nowTimestamp >= data.start_time && nowTimestamp <= data.end_time){
						data.buy_status = 1
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
					
				})
			},
			
			//出价
			purchase(){
				this.$u.throttle(async ()=>{
					const {data,i18n} = this
					//pay_type 1：一口价，2竞价
					const {pay_type,code} = data
					
					const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmPay)
					if(!ret) return
					
					this.$u.api.nft.purchase(code).then(res=>{
						this.$utils.showToast(this.$t("common.success"))
						setTimeout(()=>{
							uni.navigateTo({
								url:'/pages/nft/mine'
							})
						},1200)
					})
				},500)
			},
		},
		computed:{
			i18n(){
				return this.$t("nft")
			}
		},
		onHide() {
			if(this.interval){
				clearInterval(this.interval)
			}
		}
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
