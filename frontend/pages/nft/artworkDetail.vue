<template>
	<view class="pb-150">
		<view class="position-relative pb-100">
			<view class="white-circle box-shadow back animated fadeInLeft" @click="$utils.jump(1,'navigateBack')">
				<u-icon name="arrow-leftward" size="38"></u-icon>
			</view>
			<view class="animated fadeInDown">
				<image :src="data.image | retImageUrl" style="width: 100%;height: 40vh;" mode="aspectFill"></image>
			</view>
			<view class="d-flex-between-center position-absolute animated fadeInUp" style="top: 37vh;left: 30rpx;right: 30rpx;">
				<view class="d-flex align-items-center bg-white px-30 box-shadow" style="height: 100rpx;line-height: 100rpx;border-radius: 50rpx;" @click="$utils.jump('/pages/nft/artistDetail?uid='+data.author)">
					<u-image :src="data.author_avatar | retImageUrl" width="70" height="70" border-radius="50%"></u-image>
					<text class="font-size-28 ml-20 font-weight-bold">{{data.author_name}}</text>
				</view>
				<view class="white-circle box-shadow" @click="collect">
					<u-icon name="heart" size="42" v-if="!data.collect"></u-icon>
					<u-icon name="heart-fill" :color="$downColor" size="42" v-else></u-icon>
				</view>
			</view>
			<view class="position-absolute animated fadeInRight" style="width: 60rpx;height: 80rpx;right: 30rpx;top: 30rpx;">
				<image :src="`../../static/image/icon/rarity-${data.rarity}.png`" class="w-100 h-100"></image>
			</view>
		</view>
		
		<view class="mx-30 font-weight-bold animated fadeInUp mb-50 d-flex-between-center" >
			<view class="d-flex align-items-center">
				<!-- <text v-if="data.type" class="type-name  font-size-22 mr-20">{{data.type.name}}</text> -->
				<text class="font-size-42 d-block text-deepblue">{{data.name}}</text>
			</view>
			<text class="tag type-pay_type" v-if="data.pay_type_name" :class="data.pay_type_name.color" >{{data.pay_type_name.name}}</text>	
		</view>
		
		<view class="mx-30  animated fadeInUp mb-50 d-flex align-items-baseline">
			<text class="d-block opacity-50" >{{i18n.price}}</text>
			<view class=" text-deepblue d-flex align-items-baseline font-weight-bold ml-20">
				<text class="font-size-50">{{+data.price}}</text>
				<text class="font-size-32 ml-10">{{data.currency_name}}</text>
			</view>
		</view>
		
		<view class="mx-30 mb-50 " v-if="data.description">
			<text class="d-block opacity-50" >{{i18n.DESCRIPTION}}</text>
			<text class="d-block mt-24 text-deepblue font-weight-bold">{{data.description}}</text>
		</view>
		
		<view class="mx-30 mb-50  animated fadeInUp">
			<view class="d-flex align-items-center">
				<text class="d-block opacity-50">{{i18n.ADDRESS}}</text>
				<text class="copy ml-20">{{i18n.copy}}</text>
			</view>
			<text class="d-block mt-24 text-deepblue font-weight-bold" style=" word-wrap: break-word;">{{data.code}}</text>
		</view>
		
		<view class="mx-30 mb-50 animated fadeInUp" v-if="data.pay_type != 3">
			<text class="d-block opacity-50">{{i18n.CURRENT_BID}}</text>
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
		
		<view class="fixed-nav animated bounceIn" @click="jump" v-if="data.buy_status == 1">
			{{data.pay_type == 1 ? i18n.purchase : i18n.placeABid}}
		</view>
		
		<view class="fixed-nav fixed-nav-2 font-size-32 animated bounceIn" v-else-if="data.buy_status == 2">
			<block v-if="$utils.stamp2Time(data.remainTime,'d') == '00'">
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
			</block>
			<view v-else class="font-size-28">
				{{data.start_time | date('yyyy-mm-dd hh:MM:ss')}}
				{{i18n.open}}{{data.pay_type == 1 ? i18n.purchase : i18n.placeABid}}
			</view>
		</view>
		
		<view class="fixed-nav fixed-nav-3 animated bounceIn"  v-if="data.buy_status == 0">
			{{i18n.saled}}
		</view>
	</view>
</template>
<style>
	page,html,body{
		background-color: white;
	}
</style>
<script>
	export default {
		data() {
			return {
				id:0,
				data:{},
				interval:null,
				order:[]
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
				const {i18n} = this
				this.$u.api.nft.getArtworkDetail(this.id).then(res=>{
					let data = res.message
					data.type = this.getTypeName(data.type)
					data.buy_status = this.$utils.nftBuyStatus(data.status,data.start_time,data.end_time)
					data.start_time = Date.parse(data.start_time)
					data.end_time = Date.parse(data.end_time) 
					const rarity = {'N':1,'R':2,'SR':3}
					data.rarity = data.rarity ? rarity[data.rarity] : 1
					
					// if(data.pay_type == 3) data.image = '/upload_nft/bind_box.png'
					
					if(data.buy_status != 0){
						const nowTimestamp = Date.parse(new Date())
						if(nowTimestamp < data.start_time){
							data.buy_status = 2
							data.remainTime = (data.start_time - nowTimestamp) / 1000
						}else if(nowTimestamp >= data.start_time && nowTimestamp <= data.end_time){
							data.buy_status = 1
						}
					}
					
					const buyType = {
						'1':{name:i18n.normal,color:'tag-primary'},
						'2':{name:i18n.auction,color:'tag-warning'},
						'3':{name:i18n.bindBox,color:'tag-error'}}
					data.pay_type_name = buyType[data.pay_type.toString()]
					
					
					
					this.data = data
					if(this.data.buy_status == 2){
						this.interval = setInterval(()=>{
							this.data.remainTime-- 
							if(this.data.remainTime == 0){
								data.buy_status = 1
								clearInterval(this.interval)
							}
						},1000)
					}
					//获取出价记录
					this.getPurchaseOrder(data.code)
				})
			},
			getTypeName(type){
				const {i18n} = this
				switch(type){
					case 1:
						return {type:1,name:i18n.image,color:'tag-primary'};
					case 2:
						return {type:2,name:i18n.gif,color:'tag-secondary'};
					case 3:
						return {type:3,name:i18n.audio,color:'tag-success'};
					case 4:
						return {type:4,name:i18n.video,color:'tag-error'};
				}
			},
			//收藏和取消收藏
			collect(){
				this.$u.throttle(()=>{
					this.$u.api.nft.collect(this.data.code).then(res=>{
						this.data.collect = !this.data.collect
						this.$utils.showToast(this.$t("common.success"))
					})
				},1000)
			},
			jump(){
				const {data} = this
				if(data.pay_type == 1 || data.pay_type == 3){
					//如果是一口价
					uni.navigateTo({
						url:'/pages/nft/purchase_one?id='+data.id
					})
				}else if(data.pay_type == 2){
					//如果是拍卖
					uni.navigateTo({
						url:'/pages/nft/purchase?id='+data.id
					})
				}
			},
			//获取出价记录
			getPurchaseOrder(code){
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
		onHide() {
			if(this.interval){
				clearInterval(this.interval)
			}
		}
	}
</script>

<style lang="scss">
.white-circle{
	width: 80rpx;height: 80rpx;
	border-radius: 50%;
	background-color: white;
	display: flex;
	align-items: center;
	justify-content: center;
}

.copy{
	background-color: $uni-color-deepblue;
	border-radius: 20rpx;
	color: #fff;
	font-size: 22rpx;
	font-weight: normal !important;
	padding:6rpx 20rpx;
}

.fixed-nav{
	position: fixed;
	bottom: 10vw;
	@extend .box-shadow;
	height: 100rpx;
	border-radius: 50rpx;
	transition: all .8s ease 0s;
	z-index: 999;
	left: 20vw;
	right: 20vw;
	display: flex;
	justify-content: center;
	align-items: center;
	font-weight: bold;
	font-size:36rpx;
	background-image: linear-gradient(to right, #434343 0%, black 100%);
	color: #fff;
	&.fixed-nav-2{
		background: #ffffff;
		border: none;
		color: $uni-color-deepblue;
	}
	&.fixed-nav-3{
		background: none;
		background-color: #E4E4E1;
		background-image: radial-gradient(at top center, rgba(255,255,255,0.03) 0%, rgba(0,0,0,0.03) 100%), linear-gradient(to top, rgba(255,255,255,0.1) 0%, rgba(143,152,157,0.60) 100%);
		background-blend-mode: normal, multiply;
		border: none;
	}
}

.back{
	position: absolute;
	left: 30rpx;
	top: 30rpx;
	z-index: 10;
}

.type-name{
	@extend .box-shadow;
	display: block;
	padding: 8rpx 16rpx;
	border-radius: 10rpx;
	background-image: linear-gradient(45deg, #93a5cf 0%, #e4efe9 100%);
	color: #fff;
}

.timer{
	width: 60rpx;
	height: 60rpx;
	line-height: 60rpx;
	text-align: center;
	background-color: #FF0000;
	border-radius: 16rpx;
	background: none;
	font-weight: bold;
	margin: 0 20rpx;
	background-image: linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%);
}
</style>
