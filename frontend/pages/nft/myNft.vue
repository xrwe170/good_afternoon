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
		
		<view class="mx-30  animated fadeInUp mb-50 d-flex  align-items-baseline">
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

		
		<view class="fixed-nav-wrap animated bounceIn">
			<view class="fixed-nav-item" v-if="data.resell_nft_status != 1" @click="$utils.jump('/pages/nft/resell?id='+data.id)">
				{{i18n.resell}}
			</view>
			<view class="fixed-nav-item fixed-nav-item-4" v-else >
				{{i18n.reselling}}
			</view>
			<!-- 盲盒未开启 -->
			<view class="fixed-nav-item fixed-nav-item-2" v-if="data.pay_type == 3 && data.rarity_status == 0 && showBindBoxBtn" @click="openBindBox">
				{{i18n.clickOpen}}
			</view>
			<view class="fixed-nav-item fixed-nav-item-3" v-if="data.pay_type == 3 && data.rarity_status == 1" >
				{{i18n.hasOpen}}
			</view>
		</view>
		
		<!-- 弹出盲盒的popup -->
		<u-popup v-model="showBindBox" border-radius="5" width="90%" height="90%" centerBackgroundColor="none" :maskCloseAble="false">
			<view class="d-flex align-items-center justify-content-center w-100 h-100">
				<view class="animated" v-if="showGif" :class="{bounceOut:showGifAnimate}">
					<image :src="bindBoxImageSrc" style="width: 120vw;height: 120vw;" ></image>
				</view>
				<view class="text-center animated bounceIn" v-else>
					<u-image :src="willData.image | retImageUrl" width="90vw" height="90vw" border-radius="10"></u-image>
					<text class="close-btn" @click="closeBindBox">×</text>
				</view>
			</view>
		</u-popup>
		<!-- 预加载图片 -->
		<image :src="bindBoxImageSrc" @load="loadBindBoxGif" v-show="false"></image>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id:0,
				data:{},
				interval:null,
				order:[],
				showBindBox:false,
				bindBoxImageSrc:require('../../static/image/icon/bind_box.gif'),
				showGif:false,
				showGifAnimate:false,
				willData:{},
				showBindBoxBtn:false
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
					const rarity = {'N':1,'R':2,'SR':3}
					data.rarity = data.rarity ? rarity[data.rarity] : 1
					
					const buyType = {
						'1':{name:i18n.normal,color:'tag-primary'},
						'2':{name:i18n.auction,color:'tag-warning'},
						'3':{name:i18n.bindBox,color:'tag-error'}}
					data.pay_type_name = buyType[data.pay_type.toString()]
					
					this.data = data
					
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
			
			//获取出价记录
			getPurchaseOrder(code){
				this.$u.api.nft.getPurchaseOrder(code).then(res=>{
					let list = res.message
					list.forEach(item => {
						item.price = Number((+item.price).toFixed(4))
					})
					this.order = list
				})
			},
			//开启盲盒
			async openBindBox(){
				const ret = await this.$utils.showModal(this.$t("common.hint"),this.$t("nft.confirmOpen"))
				if(!ret) return
				const code = this.data.code
				this.$u.api.nft.openBindBox(code).then(res=>{
					const ret = res.message
					this.willData = ret
					
					this.showBindBox = true
					this.showGif = true
					this.showGifAnimate = false
					setTimeout(()=>{
						this.showGifAnimate = true
					},2500)
					setTimeout(()=>{
						this.showGif = false
					},3000)
				})
			},
			closeBindBox(){
				this.data = this.willData
				this.showBindBox = false
			},
			loadBindBoxGif(e){
				this.showBindBoxBtn = true
			}
		},
		computed:{
			i18n(){
				return this.$t("nft")
			}
		},
	}
</script>
<style>
	page,html,body{
		background-color: white;
	}
</style>
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

.fixed-nav-wrap{
	position: fixed;
	bottom: 10vw;
	transition: all .8s ease 0s;
	z-index: 999;
	left: 10vw;
	right: 10vw;
	font-weight: bold;
	font-size:32rpx;
	color: #fff;
	display: flex;
	.fixed-nav-item{
		height: 80rpx;
		line-height: 80rpx;
		@extend .box-shadow;
		background-image: linear-gradient(to right, #434343 0%, black 100%);
		margin: 0 10rpx;
		border-radius: 50rpx;
		width: 100%;
		text-align: center;
		
		&.fixed-nav-item-2{
			background-image: linear-gradient(to right, #b8cbb8 0%, #b8cbb8 0%, #b465da 0%, #cf6cc9 33%, #ee609c 66%, #ee609c 100%);
		}
		
		&.fixed-nav-item-3{
			background-image: linear-gradient(to top, #9890e3 0%, #b1f4cf 100%);
		}
		
		&.fixed-nav-item-4{
			background-image: linear-gradient(45deg, #93a5cf 0%, #e4efe9 100%);
		}
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
	padding: 10rpx 20rpx;
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

.close-btn{
	width: 70rpx;
	height: 70rpx;
	color: #fff;
	border: 2rpx solid #fff;
	border-radius: 50%;
	font-size: 60rpx;
	text-align: center;
	display: block;
	line-height: 60rpx;
	margin:60rpx auto 0 auto;
}
</style>
