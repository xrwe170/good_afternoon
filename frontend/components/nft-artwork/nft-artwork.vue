<template>
	<view class="box-shadow border-radius-20 position-relative pb-50 mt-70 animated w-100" :class="showData.show ? 'fadeInUp' : 'fadeOutLeft'"  @click="$utils.jump('/pages/nft/artworkDetail?id='+showData.id)">
		<view class="position-relative bg-white" style="top: -20rpx;border-radius: 20rpx 20rpx 0 0;" >
			<u-image :src="showData.image | retImageUrl" width="100%" height="314" border-radius="10"></u-image>
			<image class="ml-4 position-absolute" :src="`../../static/image/icon/rarity-${showData.rarity}.png`" style="width: 39rpx;height: 52rpx;left: 16rpx;top: 16rpx;"></image>
			<view class="mt-20 d-flex-between-center mx-10">
				<text class="d-block font-size-32 font-weight-bold text-deepblue">{{showData.name}}</text>
				<view class="white-circle box-shadow" @click.stop="collect(showData.code)">
					<u-icon name="heart" size="28" v-if="!showData.collect"></u-icon>
					<u-icon name="heart-fill" :color="$downColor" size="28" v-else></u-icon>
				</view>
			</view>
			<!-- <text class="tag type-name" v-if="showData.type" :class="showData.type.color">{{showData.type.name}}</text> -->
			<text class="tag  type-pay_type" v-if="showData.pay_type_name" :class="showData.pay_type_name.color" >{{showData.pay_type_name.name}}</text>
			<view class="mx-10 mt-20">
				<view class="d-flex align-items-center">
					<view class="box-shadow overflow-hidden border-radius-50per">
						<u-image :src="showData.author_avatar | retImageUrl" width="50" height="50" border-radius="50%" ></u-image>
					</view>
					<view class="ml-20">
						<text class="d-block opacity-50 font-size-22">{{i18n.creator}}</text>
						<text class="d-block font-weight-bold font-size-28 mt-6 text-deepblue">{{showData.author_name || i18n.nonickname}}</text>
					</view>
				</view>
				<view class="d-flex align-items-center mt-20">
					<view class="white-circle box-shadow">
						<text class="iconfont font-size-42 opacity-75 text-primary" :class="'icon-' + showData.currency_name"></text>
					</view>
					<view class="ml-20">
						<text class="d-block opacity-50 font-size-24">{{showData.pay_type == 1 ? i18n.price : i18n.currentBid}}</text>
						<text class="d-block font-weight-bold font-size-28 mt-6 text-deepblue">{{showData.price}} {{showData.currency_name}}</text>
					</view>
				</view>
			</view>
			
		</view>
		<!-- 已开始 -->
	
		<view class="place-a-bid" v-if="showData.buy_status == 1" >
			{{showData.pay_type == 1 ? i18n.purchase : i18n.placeABid}}
		</view>
		<view class="place-a-bid place-a-bid-2" v-else-if="showData.buy_status == 2">
			{{showData.start_time | date('yyyy-mm-dd hh:MM')}}
			{{i18n.open}}{{showData.pay_type == 1 ? i18n.purchase : i18n.placeABid}}
		</view>
		<view class="place-a-bid place-a-bid-2" v-else-if="showData.buy_status == 0" >
			{{i18n.saled}}
		</view>
	</view>
</template>

<script>
	export default {
		name:"nft-artwork",
		data() {
			return {
				
			};
		},
		props:{
			data:{
				type:Object,
				required:true
			}
		},
		methods:{
			//收藏
			collect(code){
				this.$u.throttle(()=>{
					this.$u.api.nft.collect(code).then(res=>{
						this.$utils.showToast(this.$t("common.success"))
						//通知父组件已删除
						this.$emit('clickCollect',code)
					})
				},1000)
			},
			getTypeName(type){
				const {i18n} = this
				switch(type){
					case 1:
						return {type:1,name:i18n.image,color:'tag-primary'};
					case 2:
						return {type:2,name:i18n.gif,color:'tag-warning'};
					case 3:
						return {type:3,name:i18n.audio,color:'tag-success'};
					case 4:
						return {type:4,name:i18n.video,color:'tag-error'};
				}
			},
		},
		computed:{
			i18n(){
				return this.$t("nft")
			},
			showData(){
				const i18n = this.$t("nft")
				const rarity = {'N':1,'R':2,'SR':3}
				let data = this.data
				data.type = this.getTypeName(+data.type)
				data.rarity = data.rarity ? rarity[data.rarity] : 1
				// 0：已结束，1：已开始，2：未开始
				data.start_time = Date.parse(data.start_time)
				data.end_time = Date.parse(data.end_time) 
				
				let limit = 0
				data.per_increase = Number(data.per_increase)
				if(data.per_increase.toString().split('.')[1]) limit = data.per_increase.toString().split('.')[1].length
				data.price = Number(Number(data.price).toFixed(limit))
				
				const buyType = {
					'1':{name:i18n.normal,color:'tag-primary'},
					'2':{name:i18n.auction,color:'tag-warning'},
					'3':{name:i18n.bindBox,color:'tag-error'}}
				data.pay_type_name = buyType[data.pay_type.toString()]
				
				//如果是盲盒，则替换图片路径
				// if(data.pay_type == 3) data.image = '/upload_nft/bind_box.png'
				
				data.buy_status = this.$utils.nftBuyStatus(data.status,data.start_time,data.end_time)
				return data
			}
		}
	}
</script>

<style lang="scss">

.white-circle{
	width: 50rpx;height: 50rpx;
	border-radius: 50%;
	background-color: white;
	display: flex;
	align-items: center;
	justify-content: center;
}

.place-a-bid{
	left: 0;right: 0;bottom: 0;
	background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);
	border-radius: 0 0 20rpx 20rpx;
	position: absolute;
	text-align: center;
	font-size: 28rpx;
	font-weight: bold;
	height: 70rpx;
	line-height: 70rpx;
	color: $uni-color-deepblue;
	&.place-a-bid-2{
		background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
		color: #999;
		font-size: 22rpx;
	}
}

.type-name{
	position: absolute;
	right: 20rpx;
	top: 20rpx;
}

.type-pay_type{
	position: absolute;
	right: 20rpx;
	top: 20rpx;
}
</style>
